<?php
header("Content-Type: application/json");
include "conn.php";

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$taskId = isset($data["task_id"]) ? intval($data["task_id"]) : 0;
$currentUserId = isset($data["user_id"]) ? intval($data["user_id"]) : 0;

if ($taskId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid task ID"]);
    exit;
}

// Fetch task info
$sql = "SELECT t.*, p.name AS project_name, u.name AS assigned_by_name
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        JOIN users u ON t.assigned_by = u.id
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Task not found"]);
    exit;
}

$task = $result->fetch_assoc();

// Fetch task updates (text + images)
$updatesSql = "SELECT u.id, u.user_id, us.name AS user_name, u.message, u.image_url, u.created_at
               FROM task_updates u
               JOIN users us ON u.user_id = us.id
               WHERE u.task_id = ?
               ORDER BY u.created_at DESC";
$stmt2 = $conn->prepare($updatesSql);
$stmt2->bind_param("i", $taskId);
$stmt2->execute();
$updatesResult = $stmt2->get_result();

$updates = [];
while ($row = $updatesResult->fetch_assoc()) {
    $updates[] = [
        "id" => intval($row["id"]),
        "user_id" => intval($row["user_id"]),
        "user_name" => $row["user_name"],
        "message" => $row["message"],
        "image_url" => $row["image_url"],
        "created_at" => $row["created_at"]
    ];
}

// Return JSON
echo json_encode([
    "success" => true,
    "task" => [
        "id" => intval($task["id"]),
        "title" => $task["title"],
        "description" => $task["description"],
        "priority" => $task["priority"],
        "status" => $task["status"],
        "assigned_by" => intval($task["assigned_by"]),
        "assigned_by_name" => $task["assigned_by_name"],
        "assigned_to" => intval($task["assigned_to"]),
        "project_name" => $task["project_name"],
        "deadline" => $task["deadline"],
        "update_requested" => intval($task["update_requested"])
    ],
    "updates" => $updates
]);
?>
