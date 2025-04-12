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

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total_bookings, SUM(amount) AS total_revenue
        FROM booking
        WHERE hostel_id = ?
    ");
    $stmt->execute([$hostelId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="w-[100%] ml-64 pt-20 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Reports</h2>
        <?php if (isset($errorMessage)): ?>
        <p class="text-red-500"><?= htmlspecialchars($errorMessage) ?></p>
        <?php else: ?>
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800">Summary</h3>
            <p>Total Bookings: <?= $report['total_bookings'] ?></p>
            <p>Total Revenue: GHS <?= number_format($report['total_revenue'], 2) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>