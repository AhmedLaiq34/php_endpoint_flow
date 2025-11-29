<?php
header('Content-Type: application/json');
include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

$required = ['project_id', 'assigned_to', 'title'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "message" => "$field is required"]);
        exit;
    }
}

$project_id = $data['project_id'];
$assigned_to = $data['assigned_to'];
$title = $data['title'];
$description = $data['description'] ?? "";
$priority = $data['priority'] ?? "medium";
$deadline = $data['deadline'] ?? NULL;

$stmt = $conn->prepare("INSERT INTO tasks (project_id, assigned_to, title, description, priority, deadline) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $project_id, $assigned_to, $title, $description, $priority, $deadline);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task created"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>
