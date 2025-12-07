<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$projectId = $data["project_id"];
$senderId = $data["sender_id"];
$email = $data["email"];

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

$stmt = $conn->prepare("INSERT INTO project_invites (project_id, sender_id, receiver_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $projectId, $senderId, $receiverId);

if ($stmt->execute()) {

    // ✅ Only send notification AFTER DB success
    $notifyUrl = "http://192.168.1.13:3000/send-notification";

    $payload = json_encode([
        "receiver_id" => $receiverId,
        "title" => "Project Invite",
        "body" => "You received a project invite"
    ]);

    $ch = curl_init($notifyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // ✅ correct spelling
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["success" => true, "message" => "Invite sent"]);

} else {
    echo json_encode(["success" => false, "message" => "Failed to send invite"]);
}
?>
