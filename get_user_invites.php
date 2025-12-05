<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$receiverId = $data["receiver_id"];

$sql = "SELECT 
            pi.id AS invite_id,
            pi.project_id,
            pi.sender_id,
            pi.receiver_id,
            pi.timestamp,
            u.name AS sender_name,
            p.name AS project_name
        FROM project_invites pi
        JOIN users u ON pi.sender_id = u.id
        JOIN projects p ON pi.project_id = p.id
        WHERE pi.receiver_id = ?
        ORDER BY pi.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiverId);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

while($row = $result->fetch_assoc()){
    $response[] = [
        "invite_id"    => (int)$row["invite_id"],
        "project_id"   => (int)$row["project_id"],
        "sender_id"    => (int)$row["sender_id"],
        "receiver_id"  => (int)$row["receiver_id"],
        "sender_name"  => $row["sender_name"],
        "project_name" => $row["project_name"],
        "timestamp"    => $row["timestamp"]
    ];
}

echo json_encode(["success" => true, "invites" => $response]);

?>
