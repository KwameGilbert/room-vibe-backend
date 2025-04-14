<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection

// Get the logged-in student ID
$student_id = $_SESSION['student_id'];

// Fetch wishlist items for the student (retrieve only hostel_id)
$wishlistQuery = "SELECT hostel_id FROM wishlist WHERE student_id = :student_id";
$stmtWishlist = $conn->prepare($wishlistQuery);
$stmtWishlist->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmtWishlist->execute();
$wishlistRows = $stmtWishlist->fetchAll(PDO::FETCH_ASSOC);
$wishlistHostels = array_column($wishlistRows, 'hostel_id'); // Extract hostel_id values

// Fetch hostels (listing all hostels)
$query = "
SELECT
hostel.*,
school.name AS school_name
FROM hostel
LEFT JOIN school ON hostel.school_id = school.id
";
$stmt = $conn->prepare($query);
$stmt->execute();
$hostels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="hostel-list" class="grid grid-cols-1 px-3 mb-20">
    <?php foreach ($hostels as $hostel):
        // Determine the status details.
        $isAvailable = ($hostel['accomodation_status'] == 1);
        $statusText = $isAvailable ? "Available" : "Full";
        $statusClass = $isAvailable ? "bg-green-500" : "bg-red-500";
        
        // Look for any image in the cover image path
        $coverImagePath = "./hostel/images/covers/" . $hostel['id'] . "/";
        $images = (array) glob($coverImagePath . "*");
        $image = !empty($images) ? $images[0] : './hostel/images/hostels/default-image.jpg';
        
        // Get the rating as an integer.
        $rating = isset($hostel['rating']) ? floor($hostel['rating']) : 0;
        // Distance from campus, assume this field exists.
        $distance = isset($hostel['distance']) ? $hostel['distance'] : 'N/A';
        // Check if hostel is in wishlist
        $inWishlist = in_array($hostel['id'], $wishlistHostels);
    ?>
    <a href="hostel/?id=<?= htmlspecialchars($hostel['id']) ?>" class="select-none">
        <div class="hostel-item w-full flex gap-2 border-b border-t border-slate-300 py-4">
            <img alt="<?= htmlspecialchars($hostel['hostel_name']) ?>" loading="lazy" width="150" height="100"
                decoding="async" class="rounded-lg" src="<?= htmlspecialchars($image) ?>">
            <div class="w-full pr-4">
                <h1 class="hostel-name text-lg font-semibold"><?= htmlspecialchars($hostel['hostel_name']) ?></h1>
                <p class="hostel-location text-sm text-gray-500 font-semibold">
                    <?= htmlspecialchars($hostel['school_name']) ?> - <?= htmlspecialchars($hostel['location']) ?>
                </p>

                <!-- Rating and distance container -->
                <div class="flex items-center justify-between gap-3 pt-1">
                    <!-- Display filled or outline stars based on rating -->
                    <div class="flex">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <?php if ($i < $rating): ?>
                        <i class="fas fa-star text-yellow-500"></i>
                        <?php else: ?>
                        <i class="far fa-star text-yellow-500"></i>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <!-- Display distance with a map-marker icon -->
                    <div class="flex items-center text-gray-500 text-sm">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?= htmlspecialchars($distance) ?> km
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <button class="<?= $statusClass ?> px-2 text-white font-semibold rounded-md">
                        <?= $statusText ?>
                    </button>
                    <!-- Wishlist star (clickable) -->
                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-star text-yellow-500 text-2xl cursor-pointer wishlist-toggle"
                        data-hostel-id="<?= $hostel['id'] ?>" data-in-wishlist="<?= $inWishlist ? 'true' : 'false' ?>">
                    </i>
                </div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<script>
(function() {
    // Create (or get) the notification modal element only once.
    let notificationModalListing = document.getElementById('notification-modal');
    if (!notificationModalListing) {
        notificationModalListing = document.createElement('div');
        notificationModalListing.id = 'notification-modal';
        notificationModalListing.className =
            'fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50';
        notificationModalListing.style.transition = 'transform 0.3s ease-in-out';
        document.body.appendChild(notificationModalListing);
    }

    // Define the wishlist toggle function.
    function toggleWishlistHandler(event) {
        event.preventDefault();

        let element = this;
        let hostelId = element.getAttribute("data-hostel-id");
        let inWishlist = element.getAttribute("data-in-wishlist") === "true";

        fetch("./wishlist/wishlistHandler.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `hostel_id=${hostelId}&in_wishlist=${inWishlist ? 1 : 0}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle star icon
                    element.classList.toggle("fas");
                    element.classList.toggle("far");
                    element.setAttribute("data-in-wishlist", inWishlist ? "false" : "true");

                    // Show success notification
                    notificationModalListing.innerHTML = `
                    <div class="bg-green-500 text-white px-4 py-3 text-center shadow-lg">
                        <i class="fas fa-check-circle mr-3"></i>
                        ${data.message}
                    </div>
                `;
                } else {
                    // Show error notification
                    notificationModalListing.innerHTML = `
                    <div class="bg-red-500 text-white px-4 py-3 text-center shadow-lg">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        ${data.message || "Operation failed."}
                    </div>
                `;
                }
                notificationModalListing.style.transform = 'translateY(0)';
                setTimeout(() => {
                    notificationModalListing.style.transform = 'translateY(-100%)';
                }, 2000);
            })
            .catch(error => {
                console.error("Error:", error);
                notificationModalListing.innerHTML = `
                <div class="bg-red-500 text-white px-4 py-3 text-center shadow-lg">
                    Operation failed.
                </div>
            `;
                notificationModalListing.style.transform = 'translateY(0)';
                setTimeout(() => {
                    notificationModalListing.style.transform = 'translateY(-100%)';
                }, 2000);
            });
    }

    // Attach the event listener only if one isn't already attached.
    const wishlistToggles = document.querySelectorAll('.wishlist-toggle');
    wishlistToggles.forEach(el => {
        if (!el.dataset.listenerAttached) {
            el.addEventListener('click', toggleWishlistHandler);
            el.dataset.listenerAttached = 'true';
        }
    });
})();
</script>