<?php
// hostel_details.php
session_start();
if (!isset($_SESSION['student_id'])) {
    // Get the current student ID 
    $_SESSION = [];
    header("Location: ../splash.php");
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Get hostel_id from URL query parameter
if (!isset($_GET['id'])) {
    die("Hostel ID not specified.");
}
$hostel_id = $_GET['id'];

// Fetch hostel details along with school information
$stmt = $conn->prepare("SELECT h.*, s.* FROM hostel h JOIN school s ON h.school_id = s.id WHERE h.id = ?");
$stmt->execute([$hostel_id]);
$hostel = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$hostel) {
    die("Hostel not found.");
}

// Fetch amenities from the amenity table
$stmt = $conn->prepare("SELECT * FROM amenity WHERE hostel_id = ?");
$stmt->execute([$hostel_id]);
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms joined with room_type
$stmt = $conn->prepare("SELECT  r.*, rt.type_name, rt.capacity FROM room r JOIN room_types rt ON r.room_type_id = rt.room_type_id WHERE r.hostel_id = ?");
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
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#F59E0B', // Same as tailwind text-yellow-500
                    secondary: '#484848',
                    neutral: '#767676',
                    background: '#F7F7F7'
                },
                fontFamily: {
                    sans: ['Inter', 'system-ui', 'sans-serif'],
                },
                boxShadow: {
                    'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                }
            }
        }
    }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        background-color: #F7F7F7;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .slide-up {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }
    </style>
</head>


<body class="font-sans text-secondary antialiased">
    <!-- Modern floating header -->
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 px-4 py-3">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <button onclick="window.history.back()"
                    class="flex items-center justify-center w-10 rounded transition-colors">
                    <i class="fas fa-arrow-left text-primary"></i>
                </button>
                <h1 class="text-lg font-semibold text-secondary max-w-[100%]">
                    <?php echo htmlspecialchars($hostel['hostel_name']); ?>
                </h1>
            </div>
            <button onclick="shareHostel()"
                class="flex items-center bg-primary text-white px-3 py-2 rounded-full shadow hover:bg-orange-600 focus:outline-none text-sm">
                <i class="fas fa-share-alt mr-2"></i>
                <span>Share</span>
            </button>
        </div>
    </header>
    <!-- <header class="px-4 py-3 bg-white shadow-sm sticky top-0 z-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <button onclick="window.history.back()"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white/90 shadow-soft hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </button>
                <h1 class="text-lg font-semibold max-w-[100%]">
                    Sunrise Hostel</h1>
            </div>
            <button onclick="shareHostel()"
                class="flex items-center bg-primary text-white px-3 py-2 rounded-full shadow hover:bg-orange-600 focus:outline-none text-sm">
                <i class="fas fa-share-alt mr-2"></i>
                <span>Share</span>
            </button>
        </div>
    </header> -->


    <!-- Main content with padding for fixed header -->
    <main class="pt-16 pb-20 max-w-2xl mx-auto">
        <!-- Image Slider -->
        <section class="relative mb-6">
            <?php
            $image_dir = __DIR__ . "/images/hostels/{$hostel_id}";
            $hostel_images = [];

            if (is_dir($image_dir)) {
                $files = scandir($image_dir);
                if ($files) {
                    foreach ($files as $file) {
                        if ($file !== "." && $file !== "..") {
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $hostel_images[] = "./images/hostels/{$hostel_id}/{$file}";
                            }
                        }
                    }
                }
            }
            ?>

            <?php if (count($hostel_images) > 0): ?>
            <div class="relative aspect-[4/3] bg-gray-100 rounded-none">
                <div id="slides" class="h-full overflow-x-auto scrollbar-hide snap-x snap-mandatory flex">
                    <?php foreach ($hostel_images as $index => $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Hostel Image"
                        class="w-full h-full object-cover flex-shrink-0 snap-center" data-index="<?php echo $index; ?>">
                    <?php endforeach; ?>
                </div>

                <!-- Modern image counter -->
                <div class="absolute bottom-4 right-4 bg-black/60 rounded-full px-3 py-1 text-white text-sm">
                    <span id="currentSlide">1</span>/<span><?php echo count($hostel_images); ?></span>
                </div>
            </div>
            <?php else: ?>
            <div class="aspect-[4/3] bg-gray-100 rounded-none flex items-center justify-center">
                <i class="fas fa-image text-4xl text-gray-400"></i>
            </div>
            <?php endif; ?>
        </section>

        <!-- Hostel Info Card -->
        <div class="px-4">
            <section class="bg-white rounded-2xl p-6 shadow-soft mb-4">
                <div class="text-2xl font-bold text-secondary mb-2 flex justify-start">
                    <?php echo htmlspecialchars($hostel['hostel_name']); ?>
                    <?php if (!empty($hostel['rating'])): ?>
                    <div class="flex items-center gap-1 ml-2">
                        <span class="text-yellow-500 text-sm">★</span>
                        <span class="font-medium text-sm"><?php echo htmlspecialchars($hostel['rating']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-3 text-neutral">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-university w-5 text-center"></i>
                        <span><?= htmlspecialchars($hostel['name']) ?> -
                            <?= htmlspecialchars($hostel['location']) ?></span>
                    </div>

                    <?php if (!empty($hostel['distance'])): ?>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-walking w-5 text-center"></i>
                        <span><?php echo htmlspecialchars($hostel['distance']); ?> km from campus</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($hostel['address'])): ?>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-map-marker-alt w-5 text-center"></i>
                        <span><?php echo htmlspecialchars($hostel['address']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Description Section -->
            <section class="bg-white rounded-2xl p-6 shadow-soft mb-4">
                <h2 class="text-lg font-semibold mb-3">About this hostel</h2>
                <div id="descriptionText" class="text-neutral text-sm line-clamp-3">
                    <?php echo htmlspecialchars(nl2br($hostel['description']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <button id="readMore" class="text-primary font-medium text-sm mt-2">Read more</button>
            </section>

            <!-- Amenities Section -->
            <section class="bg-white rounded-2xl p-6 shadow-soft mb-4">
                <h2 class="text-lg font-semibold mb-4">What this place offers</h2>
                <?php if (count($amenities) > 0): ?>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($amenities as $amenity): ?>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check text-primary"></i>
                        <span
                            class="text-neutral text-sm"><?php echo htmlspecialchars($amenity['amenity_name']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>

            <!-- Rooms Section -->
            <section class="bg-white rounded-2xl p-6 shadow-soft mb-20">
                <h2 class="text-lg font-semibold mb-4">Available Rooms</h2>
                <?php if (count($groupedRooms) > 0): ?>
                <div class="space-y-6">
                    <?php foreach ($groupedRooms as $roomType => $roomsArray): ?>
                    <div>
                        <h3 class="text-primary font-medium mb-3"><?php
                                                                            $roomType = ucwords(str_replace("_", " ", $roomType));
                                                                            echo htmlspecialchars($roomType);
                                                                            ?></h3>
                        <div class="space-y-2">
                            <?php foreach ($roomsArray as $room): ?>
                            <div class="flex items-center justify-between p-4 rounded-lg bg-background">
                                <div>
                                    <?php if (!empty($room['specification'])): ?>
                                    <p class="text-neutral text-sm">
                                        <?php echo htmlspecialchars($room['specification']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="font-semibold">
                                    $<?php echo number_format($room['price'], 2); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <!-- Fixed Booking Button -->
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-lg p-4">
        <div class="max-w-2xl mx-auto">
            <button onclick="window.location.href='./booking.php?id=<?php echo htmlspecialchars($hostel_id); ?>'"
                class="w-full bg-primary hover:bg-orange-600 text-white font-bold py-4 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <i class="fas fa-calendar-check mr-2"></i>
                Book Now
            </button>
        </div>
    </div>

    <script>
    // Updated modern slider logic
    const slidesContainer = document.getElementById("slides");
    const slides = document.querySelectorAll("#slides img");
    const currentSlideElement = document.getElementById("currentSlide");

    let currentIndex = 0;

    function updateSlideCounter() {
        currentSlideElement.textContent = currentIndex + 1;
    }

    slidesContainer.addEventListener('scroll', () => {
        const index = Math.round(slidesContainer.scrollLeft / slidesContainer.offsetWidth);
        if (currentIndex !== index) {
            currentIndex = index;
            updateSlideCounter();
        }
    });

    // Description expand/collapse
    const readMore = document.getElementById("readMore");
    const descriptionText = document.getElementById("descriptionText");
    let expanded = false;

    readMore.addEventListener("click", function() {
        if (!expanded) {
            descriptionText.classList.remove("line-clamp-3");
            readMore.textContent = "Show less";
        } else {
            descriptionText.classList.add("line-clamp-3");
            readMore.textContent = "Read more";
        }
        expanded = !expanded;
    });
    </script>

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

</body>

</html>
</body>

</html>