<?php
header('Content-Type: application/json');
include 'conn.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "Task ID required"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task deleted"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>
