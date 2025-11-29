<?php
header('Content-Type: application/json');
include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['id'])) {
    echo json_encode(["success" => false, "message" => "Task ID is required"]);
    exit;
}

$id = $data['id'];
$title = $data['title'] ?? null;
$description = $data['description'] ?? null;
$status = $data['status'] ?? null;
$completion = $data['completion'] ?? null;
$deadline = $data['deadline'] ?? null;
$priority = $data['priority'] ?? null;

$stmt = $conn->prepare("UPDATE tasks 
                        SET title=?, description=?, status=?, completion=?, priority=?, deadline=? 
                        WHERE id=?");

$stmt->bind_param(
    "sssissi",
    $title, $description, $status, $completion, $priority, $deadline, $id
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task updated"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>
