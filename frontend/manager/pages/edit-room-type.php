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
    $roomType = null;
} else {
    $hostelId = $hostel['id'];
    
    // Check if room type ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: manage-room-types.php');
        exit();
    }
    
    $roomTypeId = $_GET['id'];
    
    // Fetch room type details
    $stmt = $conn->prepare("
        SELECT * FROM room_types 
        WHERE room_type_id = ? AND hostel_id = ?
    ");
    $stmt->execute([$roomTypeId, $hostelId]);
    $roomType = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$roomType) {
        $errorMessage = "Room type not found or access denied.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $roomType) {
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
        
        // Check if type name already exists (excluding this room type)
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM room_types 
            WHERE hostel_id = ? AND type_name = ? AND room_type_id <> ?
        ");
        $checkStmt->execute([$hostelId, $typeName, $roomTypeId]);
        $typeExists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($typeExists) {
            throw new Exception("Another room type with this name already exists.");
        }
        
        // Update room type
        $stmt = $conn->prepare("
            UPDATE room_types 
            SET type_name = ?, capacity = ?
            WHERE room_type_id = ? AND hostel_id = ?
        ");
        
        $result = $stmt->execute([
            $typeName, $capacity, $roomTypeId, $hostelId
        ]);
        
        if ($result) {
            $successMessage = "Room type updated successfully.";
            
            // Refresh room type data
            $stmt = $conn->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
            $stmt->execute([$roomTypeId]);
            $roomType = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception("Failed to update room type.");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

$pageTitle = 'Edit Room Type';
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<!-- Main Content Area -->
<div class="w-[100%] ml-64 pt-20 px-6 py-8">
    <div class="container mx-auto">
        <?php if (isset($errorMessage) && !$roomType): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
            <div class="mt-4">
                <a href="manage-room-types.php" class="text-blue-600 hover:underline">Back to Room Types</a>
            </div>
        </div>
        <?php else: ?>
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="manage-room-types.php" class="hover:text-orange-500">Room Types</a>
            <span>/</span>
            <span>Edit Room Type</span>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit Room Type</h2>
            </div>

            <form method="POST" action="" class="p-6 space-y-6">
                <!-- Room Type Name -->
                <div>
                    <label for="type_name" class="block text-sm font-medium text-gray-700">Type Name</label>
                    <input type="text" id="type_name" name="type_name" required
                        value="<?= htmlspecialchars($roomType['type_name']) ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="capacity" name="capacity" min="1" required
                        value="<?= htmlspecialchars($roomType['capacity']) ?>"
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
                        Update Room Type
                    </button>
                </div>
            </form>
        </div>

        <?php 
            // Check if rooms are using this room type
            $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM room WHERE room_type_id = ?");
            $checkStmt->execute([$roomTypeId]);
            $roomCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            ?>

        <?php if ($roomCount > 0): ?>
        <div class="mt-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
            <p>
                <strong>Note:</strong> This room type is currently used by <?= $roomCount ?> room(s).
                Any changes made will affect these rooms as well.
            </p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>