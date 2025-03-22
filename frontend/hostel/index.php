<?php
// hostel_details.php

include_once __DIR__ . '/../config/database.php';
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection

// Get the hostel_id from URL query parameter
if (!isset($_GET['hostel_id'])) {
    die("Hostel ID not specified.");
}
$hostel_id = $_GET['hostel_id'];

// Fetch hostel details
$stmt = $conn->prepare("SELECT * FROM hostel WHERE id = ?");
$stmt->execute([$hostel_id]);
$hostel = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$hostel) {
    die("Hostel not found.");
}

// Fetch hostel images
$stmt = $conn->prepare("SELECT * FROM hostel_image WHERE hostel_id = ?");
$stmt->execute([$hostel_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct room types (with description/specification and price)
$stmt = $conn->prepare("SELECT DISTINCT room_type, price, specification FROM room WHERE hostel_id = ?");
$stmt->execute([$hostel_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch manager details (optional)
$stmt = $conn->prepare("SELECT * FROM manager WHERE id = ?");
$stmt->execute([$hostel['manager_id']]);
$manager = $stmt->fetch(PDO::FETCH_ASSOC);

// Static amenities (or fetch from a table if available)
$amenities = [
    "Free Wi-Fi",
    "24/7 Security",
    "Laundry Service",
    "Common Lounge",
    "Cafeteria"
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($hostel['hostel_name']); ?> - Details</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-100">

    <!-- Back Button (WhatsApp style) -->
    <div class="px-4 py-3 bg-white shadow flex items-center">
        <button onclick="window.history.back()" class="text-[#fd7e14] text-2xl mr-3">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h1 class="text-lg font-semibold text-black">Hostel Details</h1>
    </div>

    <!-- Hostel Gallery Slider -->
    <section class="max-w-4xl mx-auto px-4 mt-4">
        <?php if (count($images) > 0): ?>
        <div class="relative" id="slider">
            <div id="slides" class="overflow-hidden relative">
                <?php foreach ($images as $index => $img): ?>
                <img src="<?php echo htmlspecialchars($img['url']); ?>" alt="Hostel Image"
                    class="w-full h-64 object-cover <?php echo $index === 0 ? '' : 'hidden'; ?>"
                    data-index="<?php echo $index; ?>">
                <?php endforeach; ?>
            </div>
            <!-- Slider Controls -->
            <button id="prev"
                class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow text-[#fd7e14]">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="next"
                class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow text-[#fd7e14]">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <?php else: ?>
        <img src="https://via.placeholder.com/800x400?text=No+Image+Available" alt="No Image"
            class="w-full h-64 object-cover">
        <?php endif; ?>
    </section>

    <!-- Hostel Details Section -->
    <section class="max-w-4xl mx-auto px-4 mt-6 bg-white p-4 shadow rounded-lg">
        <!-- Hostel Name and Location -->
        <h2 class="text-2xl font-bold text-black"><?php echo htmlspecialchars($hostel['hostel_name']); ?></h2>
        <p class="text-gray-600 mt-1">
            <i class="fas fa-map-marker-alt text-[#fd7e14]"></i> <?php echo htmlspecialchars($hostel['location']); ?>
        </p>
        <?php if (!empty($hostel['address'])): ?>
        <p class="text-gray-600 mt-1">
            <i class="fas fa-home text-[#fd7e14]"></i> <?php echo htmlspecialchars($hostel['address']); ?>
        </p>
        <?php endif; ?>
        <!-- Description -->
        <p class="mt-4 text-gray-700"><?php echo nl2br(htmlspecialchars($hostel['description'])); ?></p>

        <!-- Room Types and Prices -->
        <?php if (count($rooms) > 0): ?>
        <div class="mt-6">
            <h3 class="text-xl font-semibold text-black">Room Options</h3>
            <div class="mt-2 space-y-4">
                <?php foreach ($rooms as $room): ?>
                <div class="p-4 border rounded-lg">
                    <h4 class="text-lg font-medium text-[#fd7e14]"><?php echo htmlspecialchars($room['room_type']); ?>
                    </h4>
                    <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($room['specification']); ?></p>
                    <p class="text-black font-bold mt-2">$<?php echo number_format($room['price'], 2); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Amenities -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold text-black">Amenities</h3>
            <ul class="mt-2 list-disc list-inside text-gray-700">
                <?php foreach ($amenities as $amenity): ?>
                <li><?php echo htmlspecialchars($amenity); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Additional Information: Manager contact, rating, distance, etc. -->
        <div class="mt-6">
            <?php if (isset($manager)): ?>
            <p class="text-gray-600 text-sm">
                <i class="fas fa-user text-[#fd7e14]"></i> Managed by <?php echo htmlspecialchars($manager['name']); ?>
                (<?php echo htmlspecialchars($manager['phone']); ?>)
            </p>
            <?php endif; ?>
            <?php if (!empty($hostel['distance'])): ?>
            <p class="text-gray-600 text-sm mt-1">
                <i class="fas fa-road text-[#fd7e14]"></i> <?php echo htmlspecialchars($hostel['distance']); ?> km from
                campus
            </p>
            <?php endif; ?>
            <?php if (!empty($hostel['rating'])): ?>
            <p class="text-gray-600 text-sm mt-1">
                <i class="fas fa-star text-[#fd7e14]"></i> Rating: <?php echo htmlspecialchars($hostel['rating']); ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Book Now Button -->
        <div class="mt-6 text-center">
            <a href="booking.php?hostel_id=<?php echo $hostel_id; ?>"
                class="inline-block bg-[#fd7e14] hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 shadow">
                Book Now
            </a>
        </div>
    </section>

    <!-- JavaScript for slider functionality -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll("#slides img");
        let currentIndex = 0;

        document.getElementById("next").addEventListener("click", function() {
            slides[currentIndex].classList.add("hidden");
            currentIndex = (currentIndex + 1) % slides.length;
            slides[currentIndex].classList.remove("hidden");
        });

        document.getElementById("prev").addEventListener("click", function() {
            slides[currentIndex].classList.add("hidden");
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            slides[currentIndex].classList.remove("hidden");
        });
    });
    </script>
</body>

</html>