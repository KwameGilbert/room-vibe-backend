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
    
    // Fetch all room types for this hostel
    $stmt = $conn->prepare("
        SELECT * FROM room_types 
        WHERE hostel_id = ? 
        ORDER BY type_name ASC
    ");
    $stmt->execute([$hostelId]);
    $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process delete request if ID is provided
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $typeId = $_GET['delete'];
        
        // Check if there are rooms using this room type
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM room WHERE room_type_id = ?");
        $checkStmt->execute([$typeId]);
        $roomCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($roomCount > 0) {
            $errorMessage = "Cannot delete room type. There are {$roomCount} rooms using this type.";
        } else {
            // Delete the room type
            $deleteStmt = $conn->prepare("DELETE FROM room_types WHERE room_type_id = ? AND hostel_id = ?");
            $result = $deleteStmt->execute([$typeId, $hostelId]);
            
            if ($result) {
                $successMessage = "Room type deleted successfully.";
                
                // Refresh the list
                $stmt->execute([$hostelId]);
                $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $errorMessage = "Failed to delete room type.";
            }
        }
    }
}

$pageTitle = 'Manage Room Types';
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
        <!-- Add New Room Type Button -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Room Types</h2>
            <a href="add-room-type.php" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i> Add New Room Type
            </a>
        </div>

        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php endif; ?>

        <!-- Room Types Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <?php if (empty($roomTypes)): ?>
            <div class="p-6 text-center">
                <p class="text-gray-600">No room types defined yet. Click "Add New Room Type" to create one.</p>
            </div>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($roomTypes as $type): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($type['type_name']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($type['capacity']) ?> Person(s)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <?= htmlspecialchars(date('j M Y', strtotime($type['created_at']))) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="edit-room-type.php?id=<?= $type['room_type_id'] ?>"
                                class="text-blue-600 hover:text-blue-900 mr-4">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" onclick="confirmDelete(<?= $type['room_type_id'] ?>)"
                                class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- More Info -->
        <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">About Room Types</h3>
                <p class="mt-2 text-gray-600">
                    Room types define the categories of accommodation you offer. Examples might include:
                </p>
                <ul class="mt-2 list-disc list-inside text-gray-600">
                    <li>Single Room</li>
                    <li>Double Room</li>
                    <li>Triple Room</li>
                    <li>Quadruple Room</li>
                    <li>Ensuite Room</li>
                </ul>
                <p class="mt-2 text-gray-600">
                    Each room type has a capacity (number of students it can accommodate). Individual rooms will be
                    linked to a room type, inheriting these properties.
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(typeId) {
    if (confirm("Are you sure you want to delete this room type? This action cannot be undone.")) {
        window.location.href = `manage-room-types.php?delete=${typeId}`;
    }
}
</script>

<?php include_once '../includes/footer.php'; ?>