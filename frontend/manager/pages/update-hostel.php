<?php
require_once '../config/auth.php';
requireManagerLogin();

require_once '../../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$managerId = getManagerId();
$hostel = getManagedHostelDetails($conn, $managerId);

if (!$hostel) {
    $errorMessage = "No hostel found for this manager account.";
} else {
    $hostelId = $hostel['id'];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $hostelName = filter_var($_POST['hostel_name'], FILTER_SANITIZE_STRING);
        $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $priceMin = filter_var($_POST['price_min'], FILTER_VALIDATE_FLOAT);
        $priceMax = filter_var($_POST['price_max'], FILTER_VALIDATE_FLOAT);
        
        // Validate inputs
        if (empty($hostelName) || empty($location) || empty($address)) {
            throw new Exception("Hostel name, location, and address are required.");
        }
        
        if ($priceMin < 0 || $priceMax < $priceMin) {
            throw new Exception("Invalid price range. Maximum price must be greater than or equal to minimum price.");
        }
        
        // Update hostel
        $stmt = $conn->prepare("
            UPDATE hostel 
            SET hostel_name = ?, location = ?, address = ?, description = ?, 
                price_min = ?, price_max = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $hostelName, $location, $address, $description, 
            $priceMin, $priceMax, $hostelId
        ]);
        
        if ($result) {
            $successMessage = "Hostel information updated successfully.";
            
            // Refresh hostel data
            $hostel = getManagedHostelDetails($conn, $managerId);
        } else {
            throw new Exception("Failed to update hostel information.");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

$pageTitle = 'Update Hostel';
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<!-- Main Content Area -->
<div class="w-[100%] ml-64 pt-20 px-6 py-8">
    <div class="container mx-auto">
        <?php if (isset($errorMessage) && !$hostel): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php else: ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Update Hostel Information</h2>
            </div>

            <form method="POST" action="" class="p-6 space-y-6">
                <!-- Hostel Name -->
                <div>
                    <label for="hostel_name" class="block text-sm font-medium text-gray-700">Hostel Name</label>
                    <input type="text" id="hostel_name" name="hostel_name" required
                        value="<?= htmlspecialchars($hostel['hostel_name'] ?? '') ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" id="location" name="location" required
                        value="<?= htmlspecialchars($hostel['location'] ?? '') ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    <p class="mt-1 text-sm text-gray-500">General area/neighborhood (e.g., East Campus, West Hill)</p>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" id="address" name="address" required
                        value="<?= htmlspecialchars($hostel['address'] ?? '') ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    <p class="mt-1 text-sm text-gray-500">Full street address with landmarks</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500"><?= htmlspecialchars($hostel['description'] ?? '') ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">Detailed description of your hostel and its features</p>
                </div>

                <!-- Price Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_min" class="block text-sm font-medium text-gray-700">Minimum Price
                            (GHS)</label>
                        <input type="number" id="price_min" name="price_min" step="0.01" min="0"
                            value="<?= htmlspecialchars($hostel['price_min'] ?? '0.00') ?>"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label for="price_max" class="block text-sm font-medium text-gray-700">Maximum Price
                            (GHS)</label>
                        <input type="number" id="price_max" name="price_max" step="0.01" min="0"
                            value="<?= htmlspecialchars($hostel['price_max'] ?? '0.00') ?>"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Update Hostel
                    </button>
                </div>
            </form>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Hostel Images</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Manage your hostel's images to attract more students.</p>
                    <a href="hostel-images.php"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-images mr-2"></i> Manage Images
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Room Types</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Define the different types of rooms available in your hostel.</p>
                    <a href="manage-room-types.php"
                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-layer-group mr-2"></i> Manage Room Types
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>