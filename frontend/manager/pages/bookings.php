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
        SELECT b.id, b.created_at, s.firstName, s.lastName, r.room_number, b.amount, b.paid
        FROM booking b
        JOIN student s ON b.student_id = s.id
        JOIN room r ON b.room_id = r.id
        WHERE b.hostel_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$hostelId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>
<?php include_once '../includes/navbar.php'; ?>

<div class="ml-64 pt-16 px-6 py-8">
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800">Bookings</h2>
        <table class="min-w-full bg-white shadow-md rounded-lg mt-6">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Student</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Room</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td class="px-4 py-2"><?= htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']) ?>
                    </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($booking['room_number']) ?></td>
                    <td class="px-4 py-2">GHS <?= number_format($booking['amount'], 2) ?></td>
                    <td class="px-4 py-2">
                        <?= $booking['paid'] ? '<span class="text-green-500">Paid</span>' : '<span class="text-red-500">Pending</span>' ?>
                    </td>
                    <td class="px-4 py-2">
                        <?= htmlspecialchars(date('j M Y, g:i a', strtotime($booking['created_at']))) ?></td>
                    <td class="px-4 py-2">
                        <a href="booking-details.php?id=<?= $booking['id'] ?>"
                            class="text-blue-500 hover:underline">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>