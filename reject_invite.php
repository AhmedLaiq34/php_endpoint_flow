<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$inviteId = $data["invite_id"];

$stmt = $conn->prepare("DELETE FROM project_invites WHERE id = ?");
$stmt->bind_param("i", $inviteId);

if($stmt->execute()){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
