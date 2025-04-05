<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Validate input parameters
$roomTypeId = filter_input(INPUT_GET, 'roomTypeId', FILTER_VALIDATE_INT);
$hostelId = filter_input(INPUT_GET, 'hostelId', FILTER_VALIDATE_INT);

if (!$roomTypeId || !$hostelId) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT 
            r.id,
            r.room_number,
            r.specification,
            r.price,
            r.status
        FROM room r
        WHERE r.hostel_id = ? 
        AND r.room_type_id = ?
        AND r.status = 'available'
        AND r.price IS NOT NULL
        ORDER BY r.room_number
    ");

    $stmt->execute([$hostelId, $roomTypeId]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'rooms' => $rooms
    ]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching rooms'
    ]);
};