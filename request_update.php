<?php
header("Content-Type: application/json");
include "conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$taskId = intval($data["task_id"]);
$currentUserId = intval($data["user_id"]);

if ($taskId <= 0 || $currentUserId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid parameters"]);
    exit;
}

// Only manager or owner can request update
$sql = "SELECT pm.role 
        FROM project_members pm
        JOIN tasks t ON t.project_id = pm.project_id
        WHERE t.id = ? AND pm.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $taskId, $currentUserId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$role = $res->fetch_assoc()["role"];
if (!in_array($role, ["owner","manager"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Mark task as update requested
$updateSql = "UPDATE tasks SET update_requested = 1 WHERE id = ?";
$stmt2 = $conn->prepare($updateSql);
$stmt2->bind_param("i", $taskId);
$stmt2->execute();

echo json_encode(["success" => true, "message" => "Update requested"]);
?>
