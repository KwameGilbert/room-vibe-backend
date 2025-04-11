<?php
// wishlistComponent.php
session_start();
include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Assuming student ID is stored in session after login
$student_id = $_SESSION['student_id'];

// Query to fetch wishlist items for the student
$query = "SELECT 
        wishlist.id AS wishlist_id,
        hostel.*,
        school.name AS school_name
    FROM wishlist
    INNER JOIN hostel ON wishlist.hostel_id = hostel.id
    LEFT JOIN school ON hostel.school_id = school.id
    WHERE wishlist.student_id = :student_id
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="wishlistheader">
    <h1 class="bg-orange-400 text-white text-xl font-semibold text-center py-3 mb-4">My Wishlist</h1>
</div>

<div id="wishlist" class="grid grid-cols-1 px-3 mb-20">
    <?php if (count($wishlistItems) > 0): ?>
    <?php foreach ($wishlistItems as $item):
            $isAvailable = ($item['accomodation_status'] == 1);
            $statusText = $isAvailable ? "Available" : "Full";
            $statusClass = $isAvailable ? "bg-green-500" : "bg-red-500";
            $image = !empty($item['image']) ? $item['image'] : './hostel/images/hostels/default-image.jpg';
            $rating = isset($item['rating']) ? floor($item['rating']) : 0;
            $distance = $item['distance'] ?? 'N/A';
        ?>
    <!-- Card container with data attributes -->
    <div class="wishlist-item block transition duration-200 border-b border-t border-slate-300 py-5 cursor-pointer"
        data-hostel-id="<?= htmlspecialchars($item['id']) ?>"
        data-wishlist-id="<?= htmlspecialchars($item['wishlist_id']) ?>">
        <div class="flex gap-4">
            <img alt="<?= htmlspecialchars($item['hostel_name']) ?>" loading="lazy" width="150" height="100"
                decoding="async" class="rounded-lg" src="<?= htmlspecialchars($image) ?>">
            <div class="w-full pr-4">
                <h1 class="text-lg font-semibold text-black">
                    <?= htmlspecialchars($item['hostel_name']) ?>
                </h1>
                <p class="text-sm text-gray-500 font-semibold">
                    <?= htmlspecialchars($item['school_name']) ?> - <?= htmlspecialchars($item['location']) ?>
                </p>
                <!-- Rating and distance container -->
                <div class="flex items-center justify-between gap-3 pt-1">
                    <div class="flex">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <?php if ($i < $rating): ?>
                        <i class="fas fa-star text-yellow-500 text-lg p-0.5"></i>
                        <?php else: ?>
                        <i class="far fa-star text-yellow-500 text-lg p-0.5"></i>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="flex items-center text-gray-500 text-sm">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?= htmlspecialchars($distance) ?> km
                    </div>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <button class="<?= $statusClass ?> px-2 text-white font-semibold rounded-md">
                        <?= $statusText ?>
                    </button>
                    <!-- Wishlist toggle star (inside card) -->
                    <i class="<?= in_array($item['id'], [$item['id']]) ? 'fas' : 'far' ?> fa-star text-yellow-500 text-lg cursor-pointer wishlist-toggle"
                        data-hostel-id="<?= htmlspecialchars($item['id']) ?>" data-in-wishlist="true"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="text-center flex flex-col items-center justify-center h-max mt-20">
        <img id="storyset" src="./images/storyset/online-wishes-list-animate.svg" class="h-full w-full">
        <p class="text-center text-gray-500">Your wishlist is empty.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Notification Modal using provided template -->
<div id="notification-modal"
    class="fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50"
    style="transition: transform 0.3s ease-in-out; transform: translateY(-100%);">
    <!-- Notification content will be inserted here dynamically -->
</div>

<script>
// Function to show the notification popup using the provided template
function showNotification(message, type = "success") {
    const notificationModal = document.getElementById("notification-modal");
    const bgColor = type === "success" ? "bg-green-500" : "bg-red-500";
    notificationModal.innerHTML = `
        <div class="${bgColor} text-white px-4 py-3 text-center shadow-lg">
            ${message}
        </div>
    `;
    notificationModal.style.transform = "translateY(0)";
    setTimeout(() => {
        notificationModal.style.transform = "translateY(-100%)";
    }, 2000);
}

// Click listener for the entire card (excluding the star toggle)
document.querySelectorAll('.wishlist-item').forEach(card => {
    card.addEventListener('click', function(event) {
        // If the clicked element is the wishlist toggle, do nothing.
        if (event.target.closest('.wishlist-toggle')) return;
        const hostelId = this.getAttribute('data-hostel-id');
        window.location.href = './hostel/?id=' + hostelId;
    });
});

// Wishlist toggle event listener
document.querySelectorAll(".wishlist-toggle").forEach(item => {
    item.addEventListener("click", function(event) {
        // Prevent click from propagating to the card
        event.preventDefault();
        event.stopPropagation();

        let icon = this;
        let hostelId = icon.getAttribute("data-hostel-id");
        let inWishlist = icon.getAttribute("data-in-wishlist") === "true";

        // Send AJAX request to toggle wishlist state
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
                    // Toggle the wishlist star icon and update data attribute
                    icon.classList.toggle("fas");
                    icon.classList.toggle("far");
                    icon.setAttribute("data-in-wishlist", inWishlist ? "false" : "true");
                    showNotification(data.message, "success");
                    // If the action was removal (inWishlist was true), remove the card
                    if (inWishlist) {
                        icon.closest(".wishlist-item").remove();
                    }
                } else {
                    showNotification(data.message || "Operation failed", "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showNotification("Operation failed", "error");
            });
    });
});
</script>