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

// List of required fields
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
    'paid'
];

// Validate all required fields
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

// Sanitize and assign values
$student_id                  = $_SESSION['student_id'];
$firstName                   = trim($_POST['firstName']);
$lastName                    = trim($_POST['lastName']);
$email                       = trim($_POST['email']);
$phone                       = trim($_POST['phone']);
$amount                      = trim($_POST['amount']);
$hostel_id                   = trim($_POST['hostel_id']);
$room_id                     = trim($_POST['room_id']); // This is the selected room's ID.
$roomNumber                  = trim($_POST['roomNumber']);
$roomTypeId                  = trim($_POST['roomTypeId']);
$program                     = trim($_POST['program']);
$year_of_study               = trim($_POST['yearOfStudy']);
$sessionAcademic             = trim($_POST['session']);
$emergency_contact_name      = trim($_POST['emergency_contact_name']);
$emergency_contact_relationship = trim($_POST['emergency_contact_relationship']);
$emergency_contact_phone     = trim($_POST['emergency_contact_phone']);
$payment_reference           = trim($_POST['payment_reference']);
$paid                        = trim($_POST['paid']); // e.g., "1" indicates paid

// Include your Database connection file
include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Prepare the INSERT statement
    $stmt = $conn->prepare("
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
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
  
    ");
    
    // Execute the statement with the form data
    $stmt->execute([
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
        $paid
    ]);
    
    // Retrieve the booking id for confirmation
    $booking_id = $conn->lastInsertId();
    
    echo json_encode(['success' => true, 'booking_id' => $booking_id]);
} catch (PDOException $e) {
    // Return an error message if something goes wrong
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>