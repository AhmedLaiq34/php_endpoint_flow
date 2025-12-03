<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$projectId = $data["project_id"];
$senderId = $data["sender_id"];
$email = $data["email"];

// Check if user exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User does not exist"]);
    exit;
}

$row = $result->fetch_assoc();
$receiverId = $row["id"];

// Insert invite
$stmt = $conn->prepare("INSERT INTO project_invites (project_id, sender_id, receiver_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $projectId, $senderId, $receiverId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Invite sent"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to send invite"]);
}
?>
