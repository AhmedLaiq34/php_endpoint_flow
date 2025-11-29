<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$projectId = $data["project_id"];

$sql = "SELECT pm.user_id, pm.role, u.name, u.email
        FROM project_members pm
        JOIN users u ON pm.user_id = u.id
        WHERE pm.project_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();

$members = [];

while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode([
    "success" => true,
    "members" => $members
]);
