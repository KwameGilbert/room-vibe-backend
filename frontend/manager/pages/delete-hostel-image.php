<?php
require_once '../config/auth.php';
requireManagerLogin();

require_once '../../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$managerId = getManagerId();
$hostel = getManagedHostelDetails($conn, $managerId);

if ($hostel && isset($_POST['image_path'])) {
    $imagePath = "../../hostel/images/hostels/" . $hostel['id'] . "/" . $_POST['image_path'];
    if (file_exists($imagePath) && unlink($imagePath)) {
        header("Location: hostel-images.php?success=1");
    } else {
        header("Location: hostel-images.php?error=1");
    }
} else {
    header("Location: hostel-images.php?error=1");
}
exit();