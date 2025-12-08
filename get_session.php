<?php
include 'conn.php'; 

$user_id = $_POST['user_id'];
$project_id = $_POST['project_id'];

if(empty($user_id) || empty($project_id)){
    echo json_encode(['status'=>'error','message'=>'Invalid parameters']);
    exit;
}

$sql = "SELECT session_start, total_seconds FROM user_project_time WHERE user_id=? AND project_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    // FIX: Use the data from $row
    echo json_encode([
        'status'=>'success', 
        'session_start'=> $row['session_start'], // Can be null or a date string
        'total_seconds'=> $row['total_seconds']
    ]);
} else {
    // Kotlin code expects this message to know it's a new record
    echo json_encode(['status'=>'error', 'message'=>'No session found']);
}
?>