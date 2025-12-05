<?php
include 'conn.php'; // your DB connection

$user_id = $_POST['user_id'];
$project_id = $_POST['project_id'];

// Validate input
if(empty($user_id) || empty($project_id)){
    echo json_encode(['status'=>'error','message'=>'Invalid parameters']);
    exit;
}

// Check if row exists for this user-project
$sql = "SELECT * FROM user_project_time WHERE user_id=? AND project_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    // If a session is already active, return error
    if($row['session_start'] !== null){
        echo json_encode(['status' => 'error', 'message' => 'Session already started']);
        exit;
    } else {
        // Start new session without changing total_seconds
        $sql = "UPDATE user_project_time SET session_start=NOW(), updated_at=NOW() WHERE user_id=? AND project_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $project_id);
        $stmt->execute();
        echo json_encode(['status'=>'success', 'message'=>'Session started']);
        exit;
    }
} else {
    // No row exists, create it with session_start
    $sql = "INSERT INTO user_project_time (user_id, project_id, session_start, total_seconds, created_at, updated_at) VALUES (?, ?, NOW(), 0, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $project_id);
    $stmt->execute();
    echo json_encode(['status'=>'success', 'message'=>'Session started']);
    exit;
}
?>
