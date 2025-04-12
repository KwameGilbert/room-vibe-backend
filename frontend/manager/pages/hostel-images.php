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

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hostel_image'])) {
    $targetDir = "../../uploads/hostel_images/";
    $fileName = basename($_FILES['hostel_image']['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['hostel_image']['tmp_name'], $targetFilePath)) {
        $stmt = $conn->prepare("INSERT INTO hostel_image (hostel_id, image_path, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$hostelId, $fileName]);
        $successMessage = "Image uploaded successfully.";
    } else {
        $errorMessage = "Failed to upload image.";
    }
}

// Fetch existing images
$stmt = $conn->prepare("SELECT * FROM hostel_image WHERE hostel_id = ?");
$stmt->execute([$hostelId]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="ml-64 pt-16 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Manage Hostel Images</h2>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6 bg-white shadow-md rounded-lg">
            <div>
                <label for="hostel_image" class="block text-sm font-medium text-gray-700">Upload Image</label>
                <input type="file" id="hostel_image" name="hostel_image" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Upload</button>
            </div>
        </form>

        <h3 class="text-xl font-semibold text-gray-800 mt-8">Existing Images</h3>
        <div class="grid grid-cols-3 gap-4 mt-4">
            <?php foreach ($images as $image): ?>
            <div class="relative">
                <img src="../../uploads/hostel_images/<?= htmlspecialchars($image['image_path']) ?>" alt="Hostel Image"
                    class="w-full h-32 object-cover rounded-md shadow-md">
                <form method="POST" action="delete-hostel-image.php" class="absolute top-2 right-2">
                    <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                    <button type="submit"
                        class="bg-red-500 text-white text-xs px-2 py-1 rounded-md hover:bg-red-600">Delete</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>