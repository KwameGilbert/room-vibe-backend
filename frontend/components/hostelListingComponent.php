<?php

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection

// Get the logged-in student ID
$student_id = $_SESSION['student_id'] ?? 1;

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
        // For the image, assume the image field stores a valid URL or use a default image.
        $image = !empty($hostel['image']) ? $hostel['image'] : './images/default-image.jpg';
        // Get the rating as an integer.
        $rating = isset($hostel['rating']) ? floor($hostel['rating']) : 0;
        // Distance from campus, assume this field exists.
        $distance = isset($hostel['distance']) ? $hostel['distance'] : 'N/A';
        // Check if hostel is in wishlist
        $inWishlist = in_array($hostel['id'], $wishlistHostels);
    ?>
    <a href="hostel/?id=<?= htmlspecialchars($hostel['id']) ?>" class="select-none">
        <div class="w-full flex gap-4 border-b border-t border-slate-300 py-5">
            <img alt="<?= htmlspecialchars($hostel['hostel_name']) ?>" loading="lazy" width="150" height="100"
                decoding="async" class="rounded-lg" src="<?= htmlspecialchars($image) ?>">
            <div class="w-full pr-4">
                <h1 class="text-lg font-semibold"><?= htmlspecialchars($hostel['hostel_name']) ?></h1>
                <p class="text-sm text-gray-500 font-semibold">
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
                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-star text-yellow-500 text-xl cursor-pointer wishlist-toggle"
                        data-hostel-id="<?= $hostel['id'] ?>" data-in-wishlist="<?= $inWishlist ? 'true' : 'false' ?>"
                        onclick=" toggleWishlist(event, this)">
                    </i>
                </div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
ease-in-out';
document.body.appendChild(notificationModalListing);
}
on failed."}
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
el.dataset.listenerAttached = 'true'