<?php
session_start();
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

// List of required fields, now including "notes"
$requiredFields = [
    'firstName',
    'lastName',
    'email',
    'phone',
    'amount',
    'hostel_id',
    'room_id',
    'roomNumber',
    'roomTypeId',
    'program',
    'yearOfStudy',
    'session',
    'emergency_contact_name',
    'emergency_contact_relationship',
    'emergency_contact_phone',
    'payment_reference',
    'paid',
    'notes'
];

// Validate that all required fields are provided
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

// Sanitize and assign values
$student_id                     = $_SESSION['student_id'];
$firstName                      = trim($_POST['firstName']);
$lastName                       = trim($_POST['lastName']);
$email                          = trim($_POST['email']);
$phone                          = trim($_POST['phone']);
$amount                         = trim($_POST['amount']);
$hostel_id                      = trim($_POST['hostel_id']);
$room_id                        = trim($_POST['room_id']); // Selected room ID
$roomNumber                     = trim($_POST['roomNumber']);
$roomTypeId                     = trim($_POST['roomTypeId']);
$program                        = trim($_POST['program']);
$year_of_study                  = trim($_POST['yearOfStudy']);
$sessionAcademic                = trim($_POST['session']);
$emergency_contact_name         = trim($_POST['emergency_contact_name']);
$emergency_contact_relationship = trim($_POST['emergency_contact_relationship']);
$emergency_contact_phone        = trim($_POST['emergency_contact_phone']);
$payment_reference              = trim($_POST['payment_reference']);
$paid                           = trim($_POST['paid']); // e.g., "1" for paid
$notes                          = trim($_POST['notes']);

// Include your Database connection file
include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Begin transaction
    $conn->beginTransaction();

    // 1. Check current active bookings for the selected room and room type
    $stmtCheck = $conn->prepare("
        SELECT COUNT(*) AS currentBookings
        FROM booking
        WHERE room_id = ? AND room_type_id = ? AND status = 'active'
    ");
    $stmtCheck->execute([$room_id, $roomTypeId]);
    $currentBookingsRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $currentBookings = (int)$currentBookingsRow['currentBookings'];

    // 2. Retrieve the capacity for this room type from the roomtypes table
    $stmtCapacity = $conn->prepare("
        SELECT capacity
        FROM room_types
        WHERE room_type_id = ? AND hostel_id = ?
    ");
    $stmtCapacity->execute([$roomTypeId, $hostel_id]);
    $capacityRow = $stmtCapacity->fetch(PDO::FETCH_ASSOC);
    
    if (!$capacityRow) {
        // If there is no matching room type, rollback
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Room type not found.']);
        exit();
    }
    $capacity = (int)$capacityRow['capacity'];

    // 3. Compare the current booking count with the capacity
    if ($currentBookings >= $capacity) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Room capacity has been reached.']);
        exit();
    }

    // 4. Insert the booking with a default status of "active"
    $stmtInsert = $conn->prepare("
        INSERT INTO booking (
            student_id, 
            first_name, 
            last_name, 
            email, 
            phone, 
            amount, 
            hostel_id, 
            room_id, 
            room_number, 
            room_type_id, 
            program, 
            year_of_study, 
            session, 
            emergency_contact_name, 
            emergency_contact_relationship, 
            emergency_contact_phone, 
            payment_reference, 
            paid, 
            status, 
            notes, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())
    ");
    $stmtInsert->execute([
        $student_id, 
        $firstName, 
        $lastName, 
        $email, 
        $phone, 
        $amount, 
        $hostel_id, 
        $room_id, 
        $roomNumber, 
        $roomTypeId, 
        $program, 
        $year_of_study, 
        $sessionAcademic, 
        $emergency_contact_name, 
        $emergency_contact_relationship, 
        $emergency_contact_phone, 
        $payment_reference, 
        $paid, 
        $notes
    ]);
    
    // Retrieve the booking id for confirmation
    $booking_id = $conn->lastInsertId();

    // 5. If adding this booking fills the capacity, update the room status to "full"
    // (currentBookings + 1 because the new booking has been added)
    if (($currentBookings + 1) >= $capacity) {
        $stmtUpdateRoom = $conn->prepare("
            UPDATE room
            SET status = 'full'
            WHERE id = ? AND hostel_id = ?
        ");
        $stmtUpdateRoom->execute([$room_id, $hostel_id]);
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode(['success' => true, 'booking_id' => $booking_id]);
} catch (PDOException $e) {
    // Roll back the transaction if something failed
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>