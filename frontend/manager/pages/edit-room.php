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
    $room = null;
} else {
    $hostelId = $hostel['id'];

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: manage-rooms.php');
        exit();
    }

    $roomId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM room WHERE id = ? AND hostel_id = ?");
    $stmt->execute([$roomId, $hostelId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        $errorMessage = "Room not found or access denied.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $room) {
    try {
        $roomNumber = filter_var($_POST['room_number'], FILTER_SANITIZE_STRING);
        $roomTypeId = filter_var($_POST['room_type_id'], FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $specification = filter_var($_POST['specification'], FILTER_SANITIZE_STRING);

        if (empty($roomNumber) || empty($roomTypeId) || $price <= 0) {
            throw new Exception("All fields are required, and price must be greater than 0.");
        }

        $stmt = $conn->prepare("
            UPDATE room 
            SET room_number = ?, room_type_id = ?, price = ?, specification = ?, updated_at = NOW() 
            WHERE id = ? AND hostel_id = ?
        ");
        $result = $stmt->execute([$roomNumber, $roomTypeId, $price, $specification, $roomId, $hostelId]);

        if ($result) {
            $successMessage = "Room updated successfully.";
            header("refresh:2;url=manage-rooms.php");
        } else {
            throw new Exception("Failed to update room.");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="w-[100%] ml-64 pt-20 px-10 py-8 bg-gray-50 min-h-screen">
    <div class="mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Room</h2>
            <a href="manage-rooms.php" class="text-orange-500 hover:text-orange-600">
                <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
            </a>
        </div>

        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($errorMessage) ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($successMessage) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room
                            Number</label>
                        <input type="text" id="room_number" name="room_number" required
                            value="<?= htmlspecialchars($room['room_number'] ?? '') ?>"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200">
                    </div>
                    <div>
                        <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                        <select id="room_type_id" name="room_type_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200">
                            <option value="">Select Room Type</option>
                            <?php
                            $stmt = $conn->prepare("SELECT room_type_id, type_name FROM room_types WHERE hostel_id = ?");
                            $stmt->execute([$hostelId]);
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['room_type_id'] == $room['room_type_id'] ? 'selected' : '';
                                echo "<option value='{$row['room_type_id']}' $selected>{$row['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (GHS)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">GHS</span>
                            <input type="number" id="price" name="price" step="0.01" required
                                value="<?= htmlspecialchars($room['price'] ?? '') ?>"
                                class="w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200">
                        </div>
                    </div>
                    <div>
                        <label for="specification"
                            class="block text-sm font-medium text-gray-700 mb-1">Specification</label>
                        <input type="text" id="specification" name="specification"
                            value="<?= htmlspecialchars($room['specification'] ?? '') ?>"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="manage-rooms.php"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Update Room
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>