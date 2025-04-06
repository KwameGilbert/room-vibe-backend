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

// Fetch booking details along with related hostel, room and student information
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Details - <?= htmlspecialchars($booking['hostel_name']) ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome (Optional for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white/70 backdrop-blur-md shadow-sm fixed top-0 left-0 right-0 p-4 z-50">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <button onclick="window.history.back()" class="text-yellow-500 hover:text-yellow-800">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800">Booking Details</h1>
            <div></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto pt-20 px-4 pb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Booking Confirmation</h2>

            <!-- Booking Information -->
            <div class="mb-4">
                <p class="text-gray-700">
                    <span class="font-semibold">Booking Reference:</span>
                    <?= htmlspecialchars($booking['payment_reference'] ?? 'N/A') ?>
                </p>
                <p class="text-gray-700">
                    <span class="font-semibold">Booking Date:</span>
                    <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($booking['created_at']))) ?>
                </p>
            </div>

            <!-- Hostel Information -->
            <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($booking['hostel_name']) ?></h3>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($booking['location']) ?></p>
                <div class="flex items-center mt-2">
                    <?php
                    $rating = isset($booking['rating']) ? floor($booking['rating']) : 0;
                    for ($i = 0; $i < 5; $i++):
                        if ($i < $rating):
                    ?>
                    <i class="fas fa-star text-yellow-500"></i>
                    <?php else: ?>
                    <i class="far fa-star text-yellow-500"></i>
                    <?php endif; endfor; ?>
                </div>
                <p class="mt-2">
                    <span
                        class="px-2 py-1 text-xs text-white rounded <?= ($booking['accomodation_status'] == 1) ? 'bg-green-500' : 'bg-red-500' ?>">
                        <?= ($booking['accomodation_status'] == 1) ? 'Available' : 'Full' ?>
                    </span>
                </p>
            </div>

            <!-- Room Information -->
            <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-2">Room Details</h3>
                <p class="text-gray-700">
                    <span class="font-semibold">Room Number:</span> <?= htmlspecialchars($booking['room_number']) ?>
                </p>
                <?php if (!empty($booking['specification'])): ?>
                <p class="text-gray-700">
                    <span class="font-semibold">Specification:</span> <?= htmlspecialchars($booking['specification']) ?>
                </p>
                <?php endif; ?>
                <p class="text-gray-700">
                    <span class="font-semibold">Price:</span> GHS <?= number_format($booking['price'], 2) ?>
                </p>
            </div>

            <!-- Student Information -->
            <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-2">Your Information</h3>
                <p class="text-gray-700">
                    <span class="font-semibold">Name:</span>
                    <?= htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']) ?>
                </p>
                <p class="text-gray-700">
                    <span class="font-semibold">Email:</span> <?= htmlspecialchars($booking['email']) ?>
                </p>
                <p class="text-gray-700">
                    <span class="font-semibold">Phone:</span> <?= htmlspecialchars($booking['phone']) ?>
                </p>
            </div>

            <!-- Additional Actions -->
            <div class="mt-6 text-center">
                <a href="./../" class="text-yellow-500 hover:underline">
                    <i class="fas fa-arrow-circle-left"></i> Back to Hostels
                </a>
            </div>
        </div>
    </main>
</body>

</html>