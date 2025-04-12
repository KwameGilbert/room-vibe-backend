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
    
    // Get filter values if provided
    $statusFilter = $_GET['status'] ?? 'all';
    $typeFilter = isset($_GET['type']) && is_numeric($_GET['type']) ? $_GET['type'] : 'all';
    
    // Build the query based on filters
    $query = "
        SELECT r.*, rt.type_name, rt.capacity 
        FROM room r
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE r.hostel_id = :hostel_id
    ";
    
    $params = ['hostel_id' => $hostelId];
    
    if ($statusFilter !== 'all') {
        $query .= " AND r.status = :status";
        $params['status'] = $statusFilter;
    }
    
    if ($typeFilter !== 'all') {
        $query .= " AND r.room_type_id = :type_id";
        $params['type_id'] = $typeFilter;
    }
    
    $query .= " ORDER BY r.room_number ASC";
    
    // Fetch rooms
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get room types for filter dropdown
    $typeStmt = $conn->prepare("
        SELECT room_type_id, type_name 
        FROM room_types 
        WHERE hostel_id = ?
        ORDER BY type_name ASC
    ");
    $typeStmt->execute([$hostelId]);
    $roomTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process status update if requested
    if (isset($_POST['update_status']) && isset($_POST['room_id']) && isset($_POST['status'])) {
        $roomId = filter_var($_POST['room_id'], FILTER_VALIDATE_INT);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
        
        if ($roomId && in_array($status, ['Available', 'Occupied', 'Maintenance', 'Reserved'])) {
            $updateStmt = $conn->prepare("
                UPDATE room 
                SET status = ?, updated_at = NOW() 
                WHERE id = ? AND hostel_id = ?
            ");
            
            $result = $updateStmt->execute([$status, $roomId, $hostelId]);
            
            if ($result) {
                $successMessage = "Room status updated successfully.";
                
                // Refresh the room list
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $errorMessage = "Failed to update room status.";
            }
        }
    }
    
    // Handle room deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $roomId = $_GET['delete'];
        
        // Check if room has active bookings
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM booking WHERE room_id = ? AND status = 'active'");
        $checkStmt->execute([$roomId]);
        $hasBookings = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($hasBookings) {
            $errorMessage = "Cannot delete room. There are active bookings for this room.";
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM room WHERE id = ? AND hostel_id = ?");
            $result = $deleteStmt->execute([$roomId, $hostelId]);
            
            if ($result) {
                $successMessage = "Room deleted successfully.";
                
                // Refresh the room list
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $errorMessage = "Failed to delete room.";
            }
        }
    }
}

$pageTitle = 'Manage Rooms';
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<!-- Main Content Area -->
<div class="ml-64 pt-16 px-6 py-8">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Manage Rooms</h1>
            <a href="add-room.php" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                <i class="fas fa-plus mr-2"></i>Add New Room
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-medium text-gray-700 mb-3">Filter Rooms</h2>
            <form action="" method="GET" class="flex flex-wrap gap-4">
                <div class="w-full sm:w-auto">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                        class="w-full sm:w-48 border border-gray-300 rounded-md shadow-sm py-2 px-3">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses
                        </option>
                        <option value="Available" <?php echo $statusFilter === 'Available' ? 'selected' : ''; ?>>
                            Available</option>
                        <option value="Occupied" <?php echo $statusFilter === 'Occupied' ? 'selected' : ''; ?>>Occupied
                        </option>
                        <option value="Maintenance" <?php echo $statusFilter === 'Maintenance' ? 'selected' : ''; ?>>
                            Maintenance</option>
                        <option value="Reserved" <?php echo $statusFilter === 'Reserved' ? 'selected' : ''; ?>>Reserved
                        </option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                    <select id="type" name="type"
                        class="w-full sm:w-48 border border-gray-300 rounded-md shadow-sm py-2 px-3">
                        <option value="all">All Types</option>
                        <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= $type['room_type_id'] ?>"
                            <?php echo $typeFilter == $type['room_type_id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($type['type_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($errorMessage) && !$hostel): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php elseif (empty($rooms)): ?>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-center py-8">
                <i class="fas fa-bed text-gray-300 text-5xl mb-3"></i>
                <h3 class="text-xl font-medium text-gray-600 mb-1">No Rooms Found</h3>
                <p class="text-gray-500 mb-4">
                    <?php if ($statusFilter !== 'all' || $typeFilter !== 'all'): ?>
                    No rooms match your current filters.
                    <?php else: ?>
                    Start by adding rooms to your hostel.
                    <?php endif; ?>
                </p>
                <?php if ($statusFilter !== 'all' || $typeFilter !== 'all'): ?>
                <a href="manage-rooms.php" class="text-orange-500 hover:underline">Clear all filters</a>
                <?php else: ?>
                <a href="add-room.php" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                    <i class="fas fa-plus mr-1"></i> Add First Room
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Rooms Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3">Room Number</th>
                            <th class="px-6 py-3">Room Type</th>
                            <th class="px-6 py-3">Capacity</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($rooms as $room): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($room['room_number']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($room['type_name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($room['capacity']) ?></td>
                            <td class="px-6 py-4">GHS <?= number_format($room['price'], 2) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    switch ($room['status']) {
                                        case 'Available':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'Occupied':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        case 'Maintenance':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'Reserved':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= htmlspecialchars($room['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <!-- Status Update Dropdown -->
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-sm border border-gray-300 rounded p-1">
                                            <option disabled selected>Update Status</option>
                                            <option value="Available">Available</option>
                                            <option value="Occupied">Occupied</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Reserved">Reserved</option>
                                        </select>
                                    </form>

                                    <!-- Edit Button -->
                                    <a href="edit-room.php?id=<?= $room['id'] ?>"
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <button onclick="confirmDelete(<?= $room['id'] ?>)"
                                        class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Room Type Management Link -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Managing Room Types</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>You need to set up room types before adding individual rooms.</p>
                        <a href="manage-room-types.php" class="block mt-1 text-blue-600 hover:underline">
                            Manage Room Types →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(roomId) {
    if (confirm("Are you sure you want to delete this room? This action cannot be undone.")) {
        window.location.href = `manage-rooms.php?delete=${roomId}`;
    }
}
</script>

<?php include_once '../includes/footer.php'; ?>