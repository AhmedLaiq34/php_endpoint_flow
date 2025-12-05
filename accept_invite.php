<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$inviteId   = $data["invite_id"];
$projectId  = $data["project_id"];
$receiverId = $data["receiver_id"];

// 1. Add to project_members
$stmt = $conn->prepare("
    INSERT INTO project_members (project_id, user_id, role)
    VALUES (?, ?, 'member')
");
$stmt->bind_param("ii", $projectId, $receiverId);

if($stmt->execute()){
    // 2. Delete invite
    $stmt2 = $conn->prepare("DELETE FROM project_invites WHERE id = ?");
    $stmt2->bind_param("i", $inviteId);
    $stmt2->execute();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Already a member or DB error"]);
}
?>
