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

<div class="w-[100%] ml-64 pt-16 px-6 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($errorMessage) ?></p>
        </div>
        <?php else: ?>
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Booking Details</h2>
                <p class="mt-1 text-sm text-gray-600">Booking ID: #<?= $booking['id'] ?></p>
            </div>
            <a href="bookings.php"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Bookings
            </a>
        </div>

        <!-- Main Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Status Banner -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Status:</span>
                        <?php if ($booking['paid']): ?>
                        <span class="ml-2 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 font-medium">
                            Paid
                        </span>
                        <?php else: ?>
                        <span class="ml-2 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 font-medium">
                            Pending Payment
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-sm text-gray-600">
                        Booked on <?= htmlspecialchars(date('j M Y, g:i a', strtotime($booking['created_at']))) ?>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <!-- Student Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Student Information</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                <span
                                    class="text-orange-600 font-medium"><?= substr($booking['firstName'], 0, 1) ?></span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($booking['email']) ?></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Phone:</span> <?= htmlspecialchars($booking['phone']) ?>
                        </p>
                    </div>
                </div>

                <!-- Booking Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Room Details</h3>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Hostel:</span> <?= htmlspecialchars($booking['hostel_name']) ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Room Number:</span>
                            <?= htmlspecialchars($booking['room_number']) ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Amount:</span>
                            <span class="text-lg font-semibold text-orange-600">
                                GHS <?= number_format($booking['amount'], 2) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <?php if (!$booking['paid']): ?>
                    <button class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Mark as Paid
                    </button>
                    <?php endif; ?>
                    <a href="generate_receipt.php?id=<?= $booking['id'] ?>" target="_blank"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                        Print Details
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>