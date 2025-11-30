<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$projectId = $data["project_id"];

$sql = "
SELECT 
    t.id, t.title, t.description, t.priority, t.status,
    t.assigned_by, t.assigned_to,
    t.deadline,
    t.completion,
    p.name AS project_name
FROM tasks t
JOIN projects p ON t.project_id = p.id
WHERE t.project_id = ?
AND t.status != 'completed'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode([
    "success" => true,
    "tasks" => $tasks
]);
