<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"];
$projectId = $data["projectId"];

// Check if user is owner
$check = $conn->prepare("SELECT owner_id FROM projects WHERE id = ?");
$check->bind_param("i", $projectId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Project does not exist"]);
    exit;
}

$row = $result->fetch_assoc();
if ($row["owner_id"] != $userId) {
    echo json_encode(["success" => false, "message" => "Only owner can delete the project"]);
    exit;
}

// Delete project (cascades to project_members and project_invites)
$stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
$stmt->bind_param("i", $projectId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete project"]);
}
?>
