<?php
header('Content-Type: application/json');
include 'conn.php';

$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    echo json_encode(["success" => false, "message" => "project_id required"]);
    exit;
}

$query = $conn->prepare("SELECT tasks.*, users.name AS assigned_user 
                         FROM tasks 
                         JOIN users ON tasks.assigned_to = users.id
                         WHERE project_id=?");
$query->bind_param("i", $project_id);
$query->execute();
$result = $query->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode(["success" => true, "tasks" => $tasks]);
?>
