<?php
// remove_room_wishlist.php
session_start();
include_once __DIR__ .'/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$wishlist_id = $data['wishlist_id'];

// Ensure the wishlist item belongs to the logged-in user
$student_id = $_SESSION['student_id'] ?? 1;

$query = "DELETE FROM wishlist WHERE id = :wishlist_id AND student_id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

$response = ['success' => false];

if ($stmt->execute()) {
    $response['success'] = true;
}

echo json_encode($response);