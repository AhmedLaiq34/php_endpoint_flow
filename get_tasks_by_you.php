<?php
header("Content-Type: application/json");
include "conn.php"; // your DB connection

// Get user_id from GET parameter
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user ID"
    ]);
    exit;
}

// Query tasks assigned by this user
$sql = "
    SELECT t.id, t.title, t.description, t.priority, t.status,t.assigned_to , t.assigned_by, p.name as project_name
FROM tasks t
JOIN projects p ON t.project_id = p.id
WHERE t.assigned_by = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = [
        "id" => intval($row['id']),
        "title" => $row['title'],
        "description" => $row['description'],
        "priority" => $row['priority'],
        "status" => $row['status'],
        "assigned_by" => intval($row['assigned_by']),
        "assigned_to" => intval($row['assigned_to']),
        "project_name" => $row['project_name']
    ];
}

if (count($tasks) > 0) {
    echo json_encode([
        "success" => true,
        "tasks" => $tasks
    ]);
} else {
    echo json_encode([
        "success" => true,
        "tasks" => [] // no tasks yet
    ]);
}
?>
