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
    // Define base paths
    $baseDir = "../.."; // Gets the root directory
    $hostelImagesDir = $baseDir . "/hostel/images/hostels/" . $hostelId;
    $coverImageDir = $baseDir . "/hostel/images/covers/" . $hostelId;
    
    // Create directories if they don't exist
    if (!file_exists($hostelImagesDir)) {
        mkdir($hostelImagesDir, 0777, true);
    }
    if (!file_exists($coverImageDir)) {
        mkdir($coverImageDir, 0777, true);
    }
}

// Define allowed file types and size limit
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Function to validate image
function validateImage($file) {
    global $allowedTypes, $maxFileSize;
    if (!in_array($file['type'], $allowedTypes)) {
        return "Invalid file type. Only JPG, JPEG & PNG files are allowed.";
    }
    if ($file['size'] > $maxFileSize) {
        return "File is too large. Maximum size is 5MB.";
    }
    return null;
}

// Handle cover image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cover_image'])) {
    $error = validateImage($_FILES['cover_image']);
    if ($error) {
        $errorMessage = $error;
    } else {
        $coverFileName = "cover_" . time() . '_' . basename($_FILES['cover_image']['name']);
        $coverFilePath = $coverImageDir . "/" . $coverFileName;
        
        try {
            // Remove old cover images
            $oldCoverImages = glob($coverImageDir . "/cover_*");
            foreach($oldCoverImages as $oldImage) {
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverFilePath)) {
                $successMessage = "Cover image uploaded successfully.";
            } else {
                throw new Exception("Failed to upload cover image.");
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}

// Handle regular image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hostel_image'])) {
    $error = validateImage($_FILES['hostel_image']);
    if ($error) {
        $errorMessage = $error;
    } else {
        try {
            $fileName = time() . '_' . basename($_FILES['hostel_image']['name']);
            $targetFilePath = $hostelImagesDir . "/" . $fileName;

            if (move_uploaded_file($_FILES['hostel_image']['tmp_name'], $targetFilePath)) {
                $successMessage = "Image uploaded successfully.";
            } else {
                throw new Exception("Failed to upload image.");
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}

// Update image display paths
$coverImage = null;
if (isset($hostelId)) {
    if (is_dir($coverImageDir)) {
        $coverImages = glob($coverImageDir . "/cover_*");
        if (!empty($coverImages)) {
            $coverImage = basename($coverImages[0]);
        }
    }
}

$images = [];
if (isset($hostelId)) {
    if (is_dir($hostelImagesDir)) {
        $images = array_diff(scandir($hostelImagesDir), array('.', '..'));
    }
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="w-[100%] ml-64 pt-20 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Manage Hostel Images</h2>

        <!-- Display Messages -->
        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
        </div>
        <?php endif; ?>

        <!-- Cover Image Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Cover Image</h3>
            <div class="p-6 bg-white shadow-md rounded-lg">
                <?php if ($coverImage): ?>
                <div class="mb-4">
                    <img src="<?= "../../hostel/images/covers/" . $hostelId . "/" . $coverImage ?>" alt="Cover Image"
                        class="w-full h-48 object-cover rounded-md">
                </div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700">
                            <?= $coverImage ? 'Change Cover Image' : 'Upload Cover Image' ?>
                        </label>
                        <input type="file" id="cover_image" name="cover_image" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            <?= $coverImage ? 'Update Cover' : 'Set Cover' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Regular Images Section -->
        <h3 class="text-xl font-semibold text-gray-800">Additional Images</h3>
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
                <img src="<?= "../../hostel/images/hostels/" . $hostelId . "/" . $image ?>" alt="Hostel Image"
                    class="w-full h-32 object-cover rounded-md shadow-md">
                <form method="POST" action="delete-hostel-image.php" class="absolute top-2 right-2">
                    <input type="hidden" name="image_path" value="<?= $image ?>">
                    <button type="submit"
                        class="bg-red-500 text-white text-xs px-2 py-1 rounded-md hover:bg-red-600">Delete</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>