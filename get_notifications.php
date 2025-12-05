<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$receiver_id = $data["receiver_id"];

// Fetch invites for this user
$sql = "SELECT pi.*, 
        u.name AS sender_name,
        p.name AS project_name
        FROM project_invites pi
        JOIN users u ON pi.sender_id = u.id
        JOIN projects p ON pi.project_id = p.id
        WHERE pi.receiver_id = ?
        ORDER BY pi.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        "id" => $row["id"],
        "sender_name" => $row["sender_name"],
        "project_name" => $row["project_name"],
        "timestamp" => $row["timestamp"]
    ];
}

echo json_encode([
    "success" => true,
    "notifications" => $notifications
]);
