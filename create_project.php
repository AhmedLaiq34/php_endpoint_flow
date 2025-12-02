<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$ownerId = $data["ownerId"];
$name = $data["name"];
$description = $data["description"];
$joinCode = $data["joinCode"];
$pictureUrl = isset($data["picture_base64"]) ? $data["picture_base64"] : ""; // new

// Insert into projects table
$stmt = $conn->prepare("INSERT INTO projects (name, description, owner_id, join_code, picture_base64) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiss", $name, $description, $ownerId, $joinCode, $pictureUrl);

if($stmt->execute()){
    $projectId = $stmt->insert_id;

    // Add owner to project_members table as 'owner'
    $stmt2 = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'owner')");
    $stmt2->bind_param("ii", $projectId, $ownerId);
    $stmt2->execute();

    echo json_encode(["success" => true]);
}else{
    echo json_encode(["success" => false, "message" => "Failed to create project"]);
}
?>
