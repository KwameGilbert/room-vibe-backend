<?php
session_start();
include_once __DIR__ . '/../config/Database.php';

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    die("Booking ID not specified.");
}
$booking_id = $_GET['id'];

// Fetch booking details
$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("
    SELECT b.*,
           h.hostel_name, h.location, h.rating, h.accomodation_status,
           r.room_number, r.price, r.specification,
           s.firstName, s.lastName, s.email, s.phone
    FROM booking b
    JOIN hostel h ON b.hostel_id = h.id
    JOIN room r ON b.room_id = r.id
    JOIN student s ON b.student_id = s.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}

// Helper function to safely output a value.
function safeOutput($value) {
    return htmlspecialchars($value ?? 'N/A');
}

// Format date
$bookingDate = date("j M Y, g:i a", strtotime($booking['created_at']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Receipt - Booking #<?= safeOutput($booking_id) ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-orange-400 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="./../images/room.png" alt="RoomVibe Logo" class="h-12 w-auto mr-3">
                    <div>
                        <h1 class="text-2xl font-bold">RoomVibe</h1>
                        <p class="text-sm">Your Campus Accommodation Partner</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm">Receipt #: <?= safeOutput($booking_id) ?></p>
                    <p class="text-sm">Date: <?= $bookingDate ?></p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-center mb-6 text-orange-600">Booking Receipt</h2>

            <!-- Payment Status -->
            <div class="bg-<?= ($booking['paid'] == 1) ? 'green-100' : 'orange-100' ?> p-4 rounded-lg mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-semibold">Payment Status</p>
                        <p class="text-xl font-bold text-<?= ($booking['paid'] == 1) ? 'green-600' : 'orange-600' ?>">
                            <?= ($booking['paid'] == 1) ? 'PAID' : 'PENDING' ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold">Amount</p>
                        <p class="text-xl font-bold">GHS <?= number_format($booking['amount'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Customer Information -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Customer Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span
                                class="font-medium"><?= safeOutput($booking['firstName'] . ' ' . $booking['lastName']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span><?= safeOutput($booking['email']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span><?= safeOutput($booking['phone']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Booking Information -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Booking Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reference:</span>
                            <span
                                class="font-medium"><?= safeOutput($booking['payment_reference'] ?? $booking['id']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span><?= $bookingDate ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Academic Program:</span>
                            <span><?= safeOutput($booking['program'] ?? 'N/A') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Session:</span>
                            <span><?= safeOutput($booking['session'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accommodation Details -->
            <div class="border rounded-lg p-4 mt-6">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Accommodation Details</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hostel:</span>
                            <span class="font-medium"><?= safeOutput($booking['hostel_name']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Location:</span>
                            <span><?= safeOutput($booking['location']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rating:</span>
                            <div class="flex">
                                <?php 
                                $rating = isset($booking['rating']) ? floor($booking['rating']) : 0;
                                for ($i = 0; $i < 5; $i++):
                                    if ($i < $rating):
                                ?>
                                <i class="fas fa-star text-orange-400"></i>
                                <?php else: ?>
                                <i class="far fa-star text-orange-400"></i>
                                <?php endif; endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Number:</span>
                            <span class="font-medium"><?= safeOutput($booking['room_number']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Type:</span>
                            <span><?= safeOutput($booking['specification'] ?? 'Standard') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Price:</span>
                            <span class="font-medium">GHS <?= number_format($booking['price'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="mt-8 text-center">
                <button onclick="window.print()"
                    class="bg-orange-400 hover:bg-orange-500 text-white py-2 px-6 rounded-lg transition">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-2">RoomVibe</h2>
                <p class="text-sm">Making campus accommodation simple</p>
                <div class="mt-4">
                    <p class="text-sm">If you have any questions, please contact our support team.</p>
                    <p class="text-sm mt-2">&copy; <?= date('Y') ?> RoomVibe. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <style>
    @media print {

        header,
        footer,
        button {
            display: none;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            background-color: white;
        }

        main {
            padding: 0;
        }

        .container {
            max-width: 100%;
            width: 100%;
        }

        .bg-white {
            box-shadow: none !important;
        }
    }
    </style>
</body>

</html>