<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"];

$sql = "SELECT p.id, p.name, p.description, p.picture_base64, pm.role,
       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as membersCount
       FROM projects p
       JOIN project_members pm ON p.id = pm.project_id
       WHERE pm.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

if(count($projects) > 0){
    echo json_encode(["success" => true, "projects" => $projects]);
}else{
    echo json_encode(["success" => false]);
}
