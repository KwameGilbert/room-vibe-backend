<?php
require_once '../config/auth.php';
requireManagerLogin();

require_once '../../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: bookings.php');
    exit();
}

$bookingId = $_GET['id'];

$stmt = $conn->prepare("
    SELECT b.*, s.firstName, s.lastName, s.email, s.phone, r.room_number, h.hostel_name
    FROM booking b
    JOIN student s ON b.student_id = s.id
    JOIN room r ON b.room_id = r.id
    JOIN hostel h ON b.hostel_id = h.id
    WHERE b.id = ?
");
$stmt->execute([$bookingId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    $errorMessage = "Booking not found.";
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="ml-64 pt-16 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Booking Details</h2>
        <?php if (isset($errorMessage)): ?>
        <p class="text-red-500"><?= htmlspecialchars($errorMessage) ?></p>
        <?php else: ?>
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800">Student Information</h3>
            <p>Name: <?= htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']) ?></p>
            <p>Email: <?= htmlspecialchars($booking['email']) ?></p>
            <p>Phone: <?= htmlspecialchars($booking['phone']) ?></p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Booking Information</h3>
            <p>Hostel: <?= htmlspecialchars($booking['hostel_name']) ?></p>
            <p>Room: <?= htmlspecialchars($booking['room_number']) ?></p>
            <p>Amount: GHS <?= number_format($booking['amount'], 2) ?></p>
            <p>Status: <?= $booking['paid'] ? 'Paid' : 'Pending' ?></p>
            <p>Date: <?= htmlspecialchars(date('j M Y, g:i a', strtotime($booking['created_at']))) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>