<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"] ?? null;

if (!$userId) {
    echo json_encode(["success" => false, "message" => "userId required"]);
    exit;
}

$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "name" => $row["name"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
?>
