<?php
header('Content-Type: application/json');
include 'conn.php'; // make sure this file connects to your DB

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['task_id']) || !isset($input['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$task_id = intval($input['task_id']);
$user_id = intval($input['user_id']);

// Optional: verify if user is assigned_by (manager)
$queryCheck = $conn->prepare("SELECT assigned_by FROM tasks WHERE id = ?");
$queryCheck->bind_param("i", $task_id);
$queryCheck->execute();
$resultCheck = $queryCheck->get_result();

if ($resultCheck->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Task not found'
    ]);
    exit;
}

$row = $resultCheck->fetch_assoc();
$assigned_by = intval($row['assigned_by']);

if ($assigned_by !== $user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'You are not authorized to complete this task'
    ]);
    exit;
}

// Update task to completed
$queryUpdate = $conn->prepare("UPDATE tasks SET status = 'completed', completion = 100 WHERE id = ?");
$queryUpdate->bind_param("i", $task_id);

if ($queryUpdate->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Task marked as completed'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update task'
    ]);
}

$conn->close();
?>
