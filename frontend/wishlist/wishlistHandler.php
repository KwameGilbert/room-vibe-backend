<?php
session_start();
include_once __DIR__ . '/../config/Database.php';

$database = new Database();
$conn = $database->getConnection();

$student_id = $_SESSION['student_id'] ?? null;
$hostel_id = $_POST['hostel_id'] ?? null;
$in_wishlist = $_POST['in_wishlist'] ?? null;

if (!$student_id || !$hostel_id) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

if ($in_wishlist == 1) {
    // Remove from wishlist
    $deleteQuery = "DELETE FROM wishlist WHERE student_id = :student_id AND hostel_id = :hostel_id";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':hostel_id', $hostel_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Wishlist removed successfully"]);
} else {
    // Add to wishlist
    $insertQuery = "INSERT INTO wishlist (student_id, hostel_id) VALUES (:student_id, :hostel_id)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':hostel_id', $hostel_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Wishlist added successfully"]);
}