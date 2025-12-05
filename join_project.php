<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"];
$joinCode = $data["joinCode"];

$stmt = $conn->prepare("SELECT id FROM projects WHERE join_code = ?");
$stmt->bind_param("s", $joinCode);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $project = $result->fetch_assoc();
    $projectId = $project["id"];

    $stmt2 = $conn->prepare("SELECT id FROM project_members WHERE project_id = ? AND user_id = ?");
    $stmt2->bind_param("ii", $projectId, $userId);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if($res2->num_rows > 0){
        echo json_encode(["success" => false, "message" => "Already a member"]);
        exit;
    }

    $stmt3 = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'member')");
    $stmt3->bind_param("ii", $projectId, $userId);
    if($stmt3->execute()){
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to join project"]);
    }

}else{
    echo json_encode(["success" => false, "message" => "Invalid join code"]);
}
