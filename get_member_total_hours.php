<?php
include 'conn.php';

$user_id = $_GET['user_id'] ?? 0;
$project_id = $_GET['project_id'] ?? 0;

$response = [];

if ($user_id == 0 || $project_id == 0) {
    $response['status'] = "error";
    $response['message'] = "Missing user_id or project_id";
    echo json_encode($response);
    exit;
}

$query = $conn->prepare("
    SELECT total_seconds
    FROM user_project_time
    WHERE user_id = ? AND project_id = ?
");
$query->bind_param("ii", $user_id, $project_id);
$query->execute();   // ✅ fixed
$result = $query->get_result()->fetch_assoc();

$response['status'] = "success";
$response['total_seconds'] = $result['total_seconds'] ?? 0;

echo json_encode($response);
?>
