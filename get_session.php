<?php
include 'conn.php'; // your DB connection

$user_id = $_POST['user_id'];
$project_id = $_POST['project_id'];

// Validate input
if(empty($user_id) || empty($project_id)){
    echo json_encode(['status'=>'error','message'=>'Invalid parameters']);
    exit;
}

// Fetch current session_start and total_seconds
$sql = "SELECT session_start, total_seconds FROM user_project_time WHERE user_id=? AND project_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    echo json_encode([
        'status'=>'success', 
        'message'=>'Session ended',
        'session_start'=> $session_start, 
        'session_seconds'=>$duration, 
        'total_seconds'=>$total_seconds
    ]);
} else {
    echo json_encode(['status'=>'error', 'message'=>'No session found']);
}
?>
