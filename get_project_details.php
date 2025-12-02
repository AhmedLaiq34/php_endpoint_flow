<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$projectId = $data["projectId"];

$sql = "SELECT p.*, u.name AS owner_name, p.picture_base64
        FROM projects p
        JOIN users u ON p.owner_id = u.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $row = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "name" => $row["name"],
        "description" => $row["description"],
        "owner_id" => $row["owner_id"],
        "owner_name" => $row["owner_name"],
        "join_code" => $row["join_code"],
        "picture_base64" => $row["picture_base64"] // send it to the app
    ]);
} else {
    echo json_encode(["success" => false]);
}
?>
