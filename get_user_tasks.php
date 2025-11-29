<?php
header('Content-Type: application/json');
include 'conn.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "user_id required"]);
    exit;
}

$query = $conn->prepare("SELECT tasks.*, projects.name AS project_name
                         FROM tasks 
                         JOIN projects ON tasks.project_id = projects.id
                         WHERE assigned_to=?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode(["success" => true, "tasks" => $tasks]);
?>
