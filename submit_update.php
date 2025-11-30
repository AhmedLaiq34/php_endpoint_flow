<?php
header("Content-Type: application/json");
include "conn.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$taskId = intval($input["task_id"] ?? 0);
$userId = intval($input["user_id"] ?? 0);
$message = $input["message"] ?? "";
$imageBase64 = $input["image_url"] ?? ""; // Base64 string (raw)

$imageUrl = "";

// Store BASE64 directly in DB
if (!empty($imageBase64)) {
    $imageUrl = $imageBase64;  
}

// Insert update
$sql = "INSERT INTO task_updates (task_id, user_id, message, image_url, created_at)
        VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $taskId, $userId, $message, $imageUrl);
$success = $stmt->execute();

if ($success) {
    // Clear request flag
    $updateSql = "UPDATE tasks SET update_requested = 0 WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $taskId);
    $updateStmt->execute();
}

echo json_encode([
    "success" => $success,
    "message" => $success ? "Update submitted and request cleared" : "Failed to submit update"
]);
?>
