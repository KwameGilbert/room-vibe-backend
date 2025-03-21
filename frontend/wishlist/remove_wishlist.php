<?php
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['hostel_id'])) {
    $userId = $_SESSION['user_id'];
    $hostelId = $_POST['hostel_id'];

    $query = "DELETE FROM wishlist WHERE user_id = ? AND hostel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$userId, $hostelId]);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
}