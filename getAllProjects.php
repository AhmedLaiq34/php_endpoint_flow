<?php
header('Content-Type: application/json');
include 'conn.php';

$query = $conn->prepare("SELECT id, name FROM projects");
$query->execute();
$result = $query->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode([
    "success" => true,
    "projects" => $projects
]);
?>
