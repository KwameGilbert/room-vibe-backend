<?php
require_once '../config/auth.php';
requireManagerLogin();

require_once '../../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $bookingId = filter_var($_POST['booking_id'], FILTER_VALIDATE_INT);

    $stmt = $conn->prepare("UPDATE booking SET status = 'Closed' WHERE id = ?");
    $result = $stmt->execute([$bookingId]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Session closed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to close session.']);
    }
    exit();
}
?>