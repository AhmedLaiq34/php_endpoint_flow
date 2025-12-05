<?php
include 'conn.php'; // your DB connection

$user_id = $_GET['user_id'];
$project_id = $_GET['project_id'];

// Validate input
if(empty($user_id) || empty($project_id)){
    echo json_encode(['status'=>'error','message'=>'Invalid parameters']);
    exit;
}

// Fetch total_seconds and session_start
$sql = "SELECT total_seconds, session_start FROM user_project_time WHERE user_id=? AND project_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    $total_seconds = intval($row['total_seconds']);

    // Add ongoing session time safely
    if($row['session_start'] !== null){
        $duration = max(0, time() - strtotime($row['session_start'])); // never negative
        $total_seconds += $duration;
    }

    // Convert to HH:MM:SS
    $hours = floor($total_seconds / 3600);
    $minutes = floor(($total_seconds % 3600) / 60);
    $seconds = $total_seconds % 60;

    echo json_encode([
        'status'=>'success',
        'total_seconds'=>$total_seconds,
        'formatted'=>sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds)
    ]);
} else {
    echo json_encode(['status'=>'error', 'message'=>'No record found']);
}
?>
