<?php
header('Content-Type: application/json');
include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);
$query = $conn->prepare("SELECT id, name FROM users");
$query->execute();
$result = $query->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    "success" => true,
    "users" => $users
]);

?>