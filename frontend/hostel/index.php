<?php
// hostel_details.php

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Get hostel_id from URL query parameter
if (!isset($_GET['id'])) {
    die("Hostel ID not specified.");
}
$hostel_id = $_GET['id'];

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

// Fetch amenities from the amenity table
$stmt = $conn->prepare("SELECT * FROM amenity WHERE hostel_id = ?");
$stmt->execute([$hostel_id]);
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms joined with room_type
$stmt = $conn->prepare("SELECT r.*, rt.type_name, rt.capacity FROM room r JOIN room_types rt ON r.room_type_id = rt.room_type_id WHERE r.hostel_id = ?");
$stmt->execute([$hostel_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group rooms by type_name
$groupedRooms = [];
foreach ($rooms as $room) {
    $groupedRooms[$room['type_name']][] = $room;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($hostel['hostel_name']); ?> - Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // Extend Tailwind theme with room vibe colors
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#fd7e14',
                    /* vibrant orange */
                    dark: '#000000',
                    graycustom: '#4a4a4a'
                }
            }
        }
    }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
    body {
        background-color: rgb(240, 240, 240);
        /* Light gray background for mobile */
    }
    </style>
</head>

<body class="font-sans text-gray-800">
    <header class="px-4 py-3 bg-white shadow-sm sticky top-0 z-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <button onclick="window.history.back()" class="text-primary text-2xl mr-3 focus:outline-none">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h1 class="text-lg font-semibold truncate max-w-[60%]">
                    <?php echo htmlspecialchars($hostel['hostel_name']); ?></h1>
            </div>
            <button onclick="shareHostel()"
                class="flex items-center bg-primary text-white px-3 py-2 rounded-full shadow hover:bg-orange-600 focus:outline-none text-sm">
                <i class="fas fa-share-alt mr-2"></i>
                <span>Share</span>
            </button>
        </div>
    </header>

    <script>
    function shareHostel() {
        if (navigator.share) {
            navigator.share({
                    title: '<?php echo htmlspecialchars($hostel['hostel_name']); ?>',
                    url: window.location.href
                })
                .catch(console.error);
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('Link copied to clipboard!'))
                .catch(console.error);
        }
    }
    </script>

    <section id="image_slide">
        <?php if (count($images) > 0): ?>
        <div class="relative" id="slider">
            <div id="slides" class="overflow-hidden relative rounded">
                <?php foreach ($images as $index => $img): ?>
                <img src="<?php echo htmlspecialchars($img['url']); ?>" alt="Hostel Image"
                    class="w-full h-64 object-cover <?php echo $index === 0 ? '' : 'hidden'; ?>"
                    data-index="<?php echo $index; ?>">
                <?php endforeach; ?>
            </div>
            <button id="prev"
                class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow text-primary focus:outline-none">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="next"
                class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow text-primary focus:outline-none">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div id="slide-indicators" class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <?php foreach ($images as $index => $img): ?>
                <button class="h-2 w-2 rounded-full bg-gray-300" data-slide-index="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <img src="./../images/default-image.jpg" alt="No Image" class="w-full h-64 object-cover rounded">
        <?php endif; ?>
    </section>



    <section class="bg-white shadow-sm rounded px-4 py-4">
        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($hostel['hostel_name']); ?></h2>
        <p class="flex items-center mt-2 text-gray-600 text-sm">
            <i class="fas fa-map-marker-alt text-primary mr-1"></i>
            <?php echo htmlspecialchars($hostel['location']); ?>
        </p>
        <?php if (!empty($hostel['distance'])): ?>
        <p class="flex items-center mt-1 text-gray-600 text-sm">
            <i class="fas fa-road text-primary mr-1"></i>
            <?php echo htmlspecialchars($hostel['distance']); ?> km away
        </p>
        <?php endif; ?>
        <?php if (!empty($hostel['rating'])): ?>
        <p class="flex items-center mt-1 text-gray-600 text-sm">
            <i class="fas fa-star text-primary mr-1"></i>
            <?php echo htmlspecialchars($hostel['rating']); ?>
        </p>
        <?php endif; ?>
        <?php if (!empty($hostel['accomodation_status'])): ?>
        <p class="mt-1 text-gray-600 text-sm">
            <i class="fas fa-info-circle text-primary mr-1"></i>
            <?php echo htmlspecialchars($hostel['accomodation_status']); ?>
        </p>
        <?php endif; ?>
        <?php if (!empty($hostel['address'])): ?>
        <p class="flex items-center mt-1 text-gray-600 text-sm">
            <i class="fas fa-home text-primary mr-1"></i>
            <?php echo htmlspecialchars($hostel['address']); ?>
        </p>
        <?php endif; ?>
    </section>

    <section class="bg-white shadow-sm rounded px-4 py-4 mt-2">
        <h3 class="text-lg font-semibold mb-2">Description</h3>
        <div id="descriptionText" class="text-gray-700 text-sm line-clamp-4">
            <?php echo nl2br(htmlspecialchars($hostel['description'])); ?>
        </div>
        <a href="javascript:void(0);" id="readMore" class="text-primary mt-2 inline-block text-sm">... Read more</a>
    </section>

    <section class="bg-white shadow-sm rounded px-4 py-4 mt-2">
        <h3 class="text-lg font-semibold mb-3">Amenities</h3>
        <?php if (count($amenities) > 0): ?>
        <div class="grid grid-cols-3 gap-2">
            <?php foreach ($amenities as $amenity): ?>
            <div class="flex flex-col items-center justify-center border rounded p-2">
                <i class="fas fa-check-circle text-xl text-primary"></i>
                <span
                    class="mt-1 text-gray-700 text-center text-xs"><?php echo htmlspecialchars($amenity['amenity_name']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-600 text-sm">No amenities available.</p>
        <?php endif; ?>
    </section>

    <section class="bg-white shadow-sm rounded px-4 py-4 mt-2 mb-6">
        <h3 class="text-lg font-semibold mb-3">Rooms</h3>
        <?php if (count($groupedRooms) > 0): ?>
        <?php foreach ($groupedRooms as $roomType => $roomsArray): ?>
        <div class="mb-4">
            <h4 class="flex items-center text-base font-semibold text-primary mb-2">
                <i class="fas fa-bed mr-2"></i>
                <?php echo htmlspecialchars($roomType); ?>
            </h4>
            <?php foreach ($roomsArray as $room): ?>
            <div class="flex justify-between items-center p-2 border rounded mb-1">
                <div>
                    <?php if (!empty($room['specification'])): ?>
                    <p class="text-gray-600 text-xs"><?php echo htmlspecialchars($room['specification']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-gray-800 font-semibold text-sm">
                    $<?php echo number_format($room['price'], 2); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-gray-600 text-sm">No room information available.</p>
        <?php endif; ?>
    </section>

    <script>
    const slides = document.querySelectorAll("#slides img");
    const indicators = document.querySelectorAll("#slide-indicators button");
    let currentIndex = 0;

    function updateSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.add("hidden");
            if (i === index) {
                slide.classList.remove("hidden");
            }
        });
        indicators.forEach((indicator, i) => {
            if (i === index) {
                indicator.classList.add("bg-primary");
                indicator.classList.remove("bg-gray-300");
            } else {
                indicator.classList.remove("bg-primary");
                indicator.classList.add("bg-gray-300");
            }
        });
    }

    updateSlide(currentIndex);

    document.getElementById("next").addEventListener("click", function() {
        currentIndex = (currentIndex + 1) % slides.length;
        updateSlide(currentIndex);
    });

    document.getElementById("prev").addEventListener("click", function() {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateSlide(currentIndex);
    });

    indicators.forEach(indicator => {
        indicator.addEventListener("click", function() {
            const index = parseInt(this.dataset.slideIndex);
            currentIndex = index;
            updateSlide(currentIndex);
        });
    });

    const readMore = document.getElementById("readMore");
    const descriptionText = document.getElementById("descriptionText");
    let expanded = false;
    readMore.addEventListener("click", function() {
        if (!expanded) {
            descriptionText.classList.remove("line-clamp-4");
            readMore.textContent = "Show less";
        } else {
            descriptionText.classList.add("line-clamp-4");
            readMore.textContent = "... Read more";
        }
        expanded = !expanded;
    });
    </script>
</body>

</html>