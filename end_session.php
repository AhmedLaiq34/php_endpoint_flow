<?php
include 'conn.php'; 

$user_id = $_POST['user_id'];
$project_id = $_POST['project_id'];

if(empty($user_id) || empty($project_id)){
    echo json_encode(['status'=>'error','message'=>'Invalid parameters']);
    exit;
}

// 1. UPDATE and CALCULATE in one step
// We use TIMESTAMPDIFF to get the exact seconds between session_start and NOW()
// We only update if session_start is NOT NULL (meaning a session was active)
$sql = "UPDATE user_project_time 
        SET total_seconds = total_seconds + TIMESTAMPDIFF(SECOND, session_start, NOW()), 
            session_start = NULL, 
            updated_at = NOW() 
        WHERE user_id = ? AND project_id = ? AND session_start IS NOT NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $project_id);
$stmt->execute();

// Check if any row was actually updated
if ($stmt->affected_rows > 0) {
    
    // 2. FETCH the new total to send back to Android
    $sql_fetch = "SELECT total_seconds FROM user_project_time WHERE user_id = ? AND project_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("ii", $user_id, $project_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $row = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Session ended', 
        'total_seconds' => $row['total_seconds'] // The accurate, updated total
    ]);

} else {
    // If no rows affected, it means session_start was already NULL (User wasn't checked in)
    echo json_encode(['status' => 'error', 'message' => 'No active session to end']);
}
?>