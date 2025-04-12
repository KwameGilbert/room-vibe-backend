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
        $typeName = filter_var($_POST['type_name'], FILTER_SANITIZE_STRING);
        $capacity = filter_var($_POST['capacity'], FILTER_VALIDATE_INT);
        
        // Validate inputs
        if (empty($typeName)) {
            throw new Exception("Room type name is required.");
        }
        
        if ($capacity < 1) {
            throw new Exception("Capacity must be at least 1.");
        }
        
        // Check if type name already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM room_types WHERE hostel_id = ? AND type_name = ?");
        $checkStmt->execute([$hostelId, $typeName]);
        $typeExists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($typeExists) {
            throw new Exception("A room type with this name already exists.");
        }
        
        // Insert new room type
        $stmt = $conn->prepare("
            INSERT INTO room_types (hostel_id, type_name, capacity, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $hostelId, $typeName, $capacity
        ]);
        
        if ($result) {
            $successMessage = "Room type added successfully.";
            // Redirect after brief delay
            header("refresh:2;url=manage-room-types.php");
        } else {
            throw new Exception("Failed to add room type.");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

$pageTitle = 'Add Room Type';
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
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="manage-room-types.php" class="hover:text-orange-500">Room Types</a>
            <span>/</span>
            <span>Add New Room Type</span>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Add New Room Type</h2>
            </div>

            <form method="POST" action="" class="p-6 space-y-6">
                <!-- Room Type Name -->
                <div>
                    <label for="type_name" class="block text-sm font-medium text-gray-700">Type Name</label>
                    <input type="text" id="type_name" name="type_name" required
                        value="<?= htmlspecialchars($_POST['type_name'] ?? '') ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    <p class="mt-1 text-sm text-gray-500">E.g. Single Room, Double Room, Ensuite, etc.</p>
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="capacity" name="capacity" min="1" required
                        value="<?= htmlspecialchars($_POST['capacity'] ?? '1') ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    <p class="mt-1 text-sm text-gray-500">Number of students this room type can accommodate</p>
                </div>

                <div class="flex items-center justify-between">
                    <a href="manage-room-types.php"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Add Room Type
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>