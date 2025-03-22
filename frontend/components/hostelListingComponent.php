<?php
// hostelListingComponent.php
// Include your database connection file and create the connection
include_once __DIR__ . '/../config/database.php';
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection

// Sample query joining the hostels with the school table.
// Adjust the table/column names as necessary.
$query = "
    SELECT 
        hostel.*,
        school.name AS school_name 
    FROM hostel 
    LEFT JOIN school ON hostel.school_id = school.id
";

$stmt = $conn->prepare($query); // Prepare the statement
$stmt->execute(); // Execute the statement

// Fetch all hostel records as an associative array.
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
    ?>
    <a href="hostel/?hostel_id=<?= htmlspecialchars($hostel['id']) ?>" class="block">
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
                    <!-- Display colored stars based on rating -->
                    <div class="flex">
                        <?php for ($i = 0; $i < $rating; $i++): ?>
                        <i class="fas fa-star text-yellow-500"></i>
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
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="20"
                        width="20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M528.1 171.5L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 
                        54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 
                        33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 
                        46.4-33.7l-25-145.5 105.7-103c19-18.5 
                        8.5-50.8-17.7-54.6zM388.6 312.3l23.7 
                        138.4L288 385.4l-124.3 65.3 23.7-138.4-100.6-98 
                        139-20.2 62.2-126 62.2 126 139 
                        20.2-100.6 98z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>