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
        $roomNumber = filter_var($_POST['room_number'], FILTER_SANITIZE_STRING);
        $roomTypeId = filter_var($_POST['room_type_id'], FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $specification = filter_var($_POST['specification'], FILTER_SANITIZE_STRING);

        if (empty($roomNumber) || empty($roomTypeId) || $price <= 0) {
            throw new Exception("All fields are required, and price must be greater than 0.");
        }

        $stmt = $conn->prepare("
            INSERT INTO room (hostel_id, room_type_id, room_number, price, specification, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'Available', NOW())
        ");
        $result = $stmt->execute([$hostelId, $roomTypeId, $roomNumber, $price, $specification]);

        if ($result) {
            $successMessage = "Room added successfully.";
            header("refresh:2;url=manage-rooms.php");
        } else {
            throw new Exception("Failed to add room.");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="ml-64 pt-16 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Add New Room</h2>
        <form method="POST" action="" class="p-6 space-y-6 bg-white shadow-md rounded-lg">
            <div>
                <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                <input type="text" id="room_number" name="room_number" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
            <div>
                <label for="room_type_id" class="block text-sm font-medium text-gray-700">Room Type</label>
                <select id="room_type_id" name="room_type_id" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    <option value="">Select Room Type</option>
                    <?php
                    $stmt = $conn->prepare("SELECT room_type_id, type_name FROM room_types WHERE hostel_id = ?");
                    $stmt->execute([$hostelId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['room_type_id']}'>{$row['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price (GHS)</label>
                <input type="number" id="price" name="price" step="0.01" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
            <div>
                <label for="specification" class="block text-sm font-medium text-gray-700">Specification</label>
                <input type="text" id="specification" name="specification"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Add
                    Room</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>