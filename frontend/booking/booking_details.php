<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./../splash.php");
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    die("Booking ID not specified.");
}
$booking_id = $_GET['id'];

// Fetch complete booking details along with related hostel, room, and student information.
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Details - <?= safeOutput($booking['hostel_name']) ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- Fixed Header with Back Button -->
    <header class="bg-orange-400 text-white fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center px-4 py-3">
            <button onclick="window.history.back()" class="mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <h1 class="text-lg font-bold">Booking Details</h1>
        </div>
    </header>

    <!-- Main Content with proper spacing for fixed header -->
    <main class="pt-16 pb-20 px-4">
        <!-- Booking Status Card -->
        <div class="bg-white rounded-xl shadow mb-4 overflow-hidden">
            <div
                class="bg-<?= (isset($booking['paid']) && $booking['paid'] == 1) ? 'green-500' : 'orange-400' ?> px-4 py-3 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-sm uppercase font-bold">Payment Status</h2>
                        <p class="text-lg font-bold">
                            <?= (isset($booking['paid']) && $booking['paid'] == 1) ? 'Paid' : 'Pending' ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">Amount</p>
                        <p class="text-lg font-bold">GHS <?= number_format($booking['amount'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Booking Ref:</span>
                    <span
                        class="font-semibold"><?= safeOutput($booking['payment_reference'] ?? $booking['id']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Date:</span>
                    <span><?= safeOutput(date("j M Y, g:i a", strtotime($booking['created_at']))) ?></span>
                </div>
            </div>
        </div>

        <!-- Accordion-style sections -->
        <div class="space-y-3">
            <!-- Hostel Section -->
            <details class="bg-white rounded-xl shadow overflow-hidden" open>
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-building text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Hostel Information</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Hostel:</span>
                        <span class="font-semibold text-right"><?= safeOutput($booking['hostel_name']) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Location:</span>
                        <span class="text-right"><?= safeOutput($booking['location']) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Rating:</span>
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
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Status:</span>
                        <span
                            class="<?= ($booking['accomodation_status'] == 1) ? 'text-green-600' : 'text-red-600' ?> font-medium">
                            <?= ($booking['accomodation_status'] == 1) ? 'Available' : 'Full' ?>
                        </span>
                    </div>
                </div>
            </details>

            <!-- Room Section -->
            <details class="bg-white rounded-xl shadow overflow-hidden" open>
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-door-open text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Room Details</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Room Number:</span>
                        <span class="font-semibold"><?= safeOutput($booking['room_number']) ?></span>
                    </div>
                    <?php if (!empty($booking['specification'])): ?>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Type:</span>
                        <span><?= safeOutput($booking['specification']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Price:</span>
                        <span class="font-semibold">GHS <?= number_format($booking['price'], 2) ?></span>
                    </div>
                </div>
            </details>

            <!-- Personal Section -->
            <details class="bg-white rounded-xl shadow overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-user text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Personal Details</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Name:</span>
                        <span
                            class="font-semibold text-right"><?= safeOutput($booking['firstName'] . ' ' . $booking['lastName']) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-right break-all"><?= safeOutput($booking['email']) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Phone:</span>
                        <span><?= safeOutput($booking['phone']) ?></span>
                    </div>
                </div>
            </details>

            <!-- Academic Section -->
            <details class="bg-white rounded-xl shadow overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-graduation-cap text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Academic Information</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Program:</span>
                        <span class="text-right"><?= safeOutput($booking['program'] ?? 'N/A') ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Year:</span>
                        <span><?= safeOutput($booking['yearOfStudy'] ?? 'N/A') ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Session:</span>
                        <span><?= safeOutput($booking['session'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </details>

            <!-- Emergency Section -->
            <details class="bg-white rounded-xl shadow overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-phone-alt text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Emergency Contact</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Name:</span>
                        <span class="text-right"><?= safeOutput($booking['emergency_contact_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Relationship:</span>
                        <span><?= safeOutput($booking['emergency_contact_relationship'] ?? 'N/A') ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Phone:</span>
                        <span><?= safeOutput($booking['emergency_contact_phone'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </details>

            <!-- Additional Notes (if available) -->
            <?php if(isset($booking['notes']) && !empty($booking['notes'])): ?>
            <details class="bg-white rounded-xl shadow overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer">
                    <div class="flex items-center">
                        <i class="fas fa-sticky-note text-orange-400 mr-3"></i>
                        <h2 class="text-lg font-bold">Additional Notes</h2>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </summary>
                <div class="p-4 pt-0 border-t border-gray-100">
                    <p class="py-2"><?= safeOutput($booking['notes']) ?></p>
                </div>
            </details>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="fixed bottom-0 left-0 right-0 bg-white shadow-lg p-4">
            <div class="flex space-x-3">
                <a href="./../" class="flex-1 py-3 text-center bg-gray-200 text-gray-800 rounded-lg font-semibold">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <button onclick="downloadReceipt(<?= $booking_id ?>)"
                    class="flex-1 py-3 bg-orange-400 text-white rounded-lg font-semibold hover:bg-orange-500 transition duration-200">
                    <i class="fas fa-receipt mr-2"></i>Receipt
                </button>
            </div>
        </div>
    </main>

    <script>
    function downloadReceipt(bookingId) {
        // Show loading indicator
        const button = document.querySelector('button[onclick^="downloadReceipt"]');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
        button.disabled = true;

        // Request the PDF
        fetch(`generate_receipt.php?id=${bookingId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                // Create a link to download the PDF
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `RoomVibe_Receipt_${bookingId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                // Restore button state
                button.innerHTML = originalContent;
                button.disabled = false;
            })
            .catch(error => {
                console.error('Error generating receipt:', error);
                // Restore button state
                button.innerHTML = originalContent;
                button.disabled = false;
                alert('Failed to generate receipt. Please try again.');
            });
    }
    </script>
</body>

</html>