<?php

require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$studentId = $_SESSION['student_id'];

$query = "SELECT h.id, h.name, h.location, h.price, h.image, h.rating
          FROM wishlist w
          JOIN hostels h ON w.hostel_id = h.id
          WHERE w.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$studentId]);
$wishlistedHostels = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($wishlistedHostels) > 0) {
    foreach ($wishlistedHostels as $row) {
?>
<div class="wishlist-item bg-white rounded-lg shadow-md p-3 mb-3">
    <img src="<?php echo $row['image']; ?>" alt="Hostel Image" class="w-full h-40 object-cover rounded-lg">
    <div class="p-2">
        <h3 class="text-lg font-semibold"><?php echo $row['name']; ?></h3>
        <p class="text-gray-500"><?php echo $row['location']; ?></p>
        <div class="flex justify-between items-center mt-2">
            <span class="text-orange-500 font-bold">$<?php echo $row['price']; ?>/month</span>
            <span class="text-yellow-500"><i class="fas fa-star"></i> <?php echo $row['rating']; ?></span>
        </div>
        <button class="remove-wishlist-btn bg-red-500 text-white px-3 py-1 rounded mt-2"
            data-hostel-id="<?php echo $row['id']; ?>">Remove</button>
    </div>
</div>
<?php
    }
} else {
    echo "<p class='text-center text-gray-500'>Your wishlist is empty.</p>";
}
?>
<script>
document.querySelectorAll(".remove-wishlist-btn").forEach(button => {
    button.addEventListener("click", function() {
        const hostelId = this.getAttribute("data-hostel-id");

        fetch("remove_wishlist.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "hostel_id=" + hostelId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest(".wishlist-item").remove();
                } else {
                    alert("Failed to remove from wishlist.");
                }
            });
    });
});
</script>