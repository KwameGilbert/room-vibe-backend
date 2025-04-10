<!-- <div id="bookingheader">
    <h1 class="text-xl font-semibold text-center mt-4 mb-4">My Bookings</h1>
</div>

<div class="text-center flex flex-col items-center justify-center h-max mt-20">
    <img id="storyset" src="./images/storyset/fill-out-animate.svg" class="h-full w-full">
    <p class='text-center text-gray-500'>
        Your bookings list is empty...
    </p>
</div> -->


<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./../splash.php");
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Fetch all bookings for the current student
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("
    SELECT b.id, b.created_at, b.paid, b.amount,
           h.hostel_name, h.location, 
           r.room_number, r.specification
    FROM booking b
    JOIN hostel h ON b.hostel_id = h.id
    JOIN room r ON b.room_id = r.id
    WHERE b.student_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$student_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to safely output a value
function safeOutput($value) {
    return htmlspecialchars($value ?? 'N/A');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Bookings</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- Header -->
    <header class="bg-orange-400 text-white fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center justify-center px-4 py-3">
            <h1 class="text-lg font-bold">My Bookings</h1>
            <div class="w-8"></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 pb-20 px-4">
        <?php if (empty($bookings)): ?>
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center h-80 mt-6">
            <img src="./images/storyset/fill-out-animate.svg" class="h-48 w-48 mb-4" alt="No bookings">
            <p class="text-center text-gray-500 mb-2">Your bookings list is empty...</p>
            <a href="index.php"
                class="mt-4 px-6 py-2 bg-orange-400 text-white rounded-lg font-medium flex items-center">
                <i class="fas fa-search mr-2"></i>Browse Hostels
            </a>
        </div>
        <?php else: ?>
        <!-- Bookings List -->
        <div class="space-y-4 pb-4">
            <?php foreach ($bookings as $booking): ?>
            <!-- Booking Card -->
            <a href="./booking/booking_details.php?id=<?= $booking['id'] ?>" class="block">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <!-- Status Bar -->
                    <div class="h-2 bg-<?= ($booking['paid'] == 1) ? 'green-500' : 'orange-400' ?>"></div>

                    <!-- Booking Content -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg"><?= safeOutput($booking['hostel_name']) ?></h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1 text-orange-400"></i>
                                    <?= safeOutput($booking['location']) ?>
                                </p>
                            </div>
                            <div
                                class="bg-<?= ($booking['paid'] == 1) ? 'green-100' : 'yellow-100' ?> px-3 py-1 rounded-full text-xs font-medium text-<?= ($booking['paid'] == 1) ? 'green-800' : 'yellow-800' ?>">
                                <?= ($booking['paid'] == 1) ? 'Paid' : 'Pending' ?>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-door-open text-gray-400 mr-1"></i>
                                <span>Room <?= safeOutput($booking['room_number']) ?></span>
                                <?php if (!empty($booking['specification'])): ?>
                                <span class="mx-1">•</span>
                                <span><?= safeOutput($booking['specification']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="font-semibold">
                                GHS <?= number_format($booking['amount'] ?? 0, 2) ?>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                            <div class="text-xs text-gray-500">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= date("j M Y", strtotime($booking['created_at'])) ?>
                            </div>
                            <div class="flex items-center text-orange-400 text-sm font-medium">
                                View Details
                                <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bg-white fixed bottom-0 left-0 right-0 shadow-lg">
        <div class="flex justify-around">
            <a href="index.php" class="flex flex-col items-center py-2 px-4">
                <i class="fas fa-home text-gray-500"></i>
                <span class="text-xs mt-1 text-gray-500">Home</span>
            </a>
            <a href="search.php" class="flex flex-col items-center py-2 px-4">
                <i class="fas fa-search text-gray-500"></i>
                <span class="text-xs mt-1 text-gray-500">Search</span>
            </a>
            <a href="bookings.php" class="flex flex-col items-center py-2 px-4">
                <i class="fas fa-receipt text-orange-400"></i>
                <span class="text-xs mt-1 font-medium text-orange-400">Bookings</span>
            </a>
            <a href="profile.php" class="flex flex-col items-center py-2 px-4">
                <i class="fas fa-user text-gray-500"></i>
                <span class="text-xs mt-1 text-gray-500">Profile</span>
            </a>
        </div>
    </nav>
</body>

</html>