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
}

// Get dashboard statistics
$hostelId = $hostel['id'] ?? null;

// Total rooms
$stmt = $conn->prepare("SELECT COUNT(*) as total_rooms FROM room WHERE hostel_id = ?");
$stmt->execute([$hostelId]);
$totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total_rooms'] ?? 0;

// Available rooms
$stmt = $conn->prepare("SELECT COUNT(*) as available_rooms FROM room WHERE hostel_id = ? AND status = 'Available'");
$stmt->execute([$hostelId]);
$availableRooms = $stmt->fetch(PDO::FETCH_ASSOC)['available_rooms'] ?? 0;

// Total bookings
$stmt = $conn->prepare("SELECT COUNT(*) as total_bookings FROM booking WHERE hostel_id = ?");
$stmt->execute([$hostelId]);
$totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['total_bookings'] ?? 0;

// Recent bookings
$stmt = $conn->prepare("
    SELECT b.*, s.firstName, s.lastName, r.room_number 
    FROM booking b
    JOIN student s ON b.student_id = s.id
    JOIN room r ON b.room_id = r.id
    WHERE b.hostel_id = ?
    ORDER BY b.created_at DESC
    LIMIT 5
");
$stmt->execute([$hostelId]);
$recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total revenue
$stmt = $conn->prepare("SELECT SUM(amount) as total_revenue FROM booking WHERE hostel_id = ? AND paid = 1");
$stmt->execute([$hostelId]);
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

// Page title
$pageTitle = 'Dashboard';
?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="w-[100%] ml-64 pt-20 px-6 py-8">
    <div class="container mx-auto">
        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php else: ?>
        <!-- Hostel Info Summary -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800"><?= htmlspecialchars($hostel['hostel_name']) ?></h2>
            <p class="text-gray-600"><?= htmlspecialchars($hostel['location']) ?></p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Rooms -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Rooms</p>
                        <h3 class="text-3xl font-bold"><?= $totalRooms ?></h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-door-open text-blue-500"></i>
                    </div>
                </div>
            </div>

            <!-- Available Rooms -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Available Rooms</p>
                        <h3 class="text-3xl font-bold"><?= $availableRooms ?></h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Bookings</p>
                        <h3 class="text-3xl font-bold"><?= $totalBookings ?></h3>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-check text-purple-500"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Revenue</p>
                        <h3 class="text-3xl font-bold">GHS <?= number_format($totalRevenue, 2) ?></h3>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-coins text-orange-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Recent Bookings</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($recentBookings)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent bookings.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recentBookings as $booking): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($booking['room_number']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= htmlspecialchars(date('M j, Y', strtotime($booking['created_at']))) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">GHS <?= number_format($booking['amount'], 2) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($booking['paid']): ?>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                                <?php else: ?>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Pending
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="booking-details.php?id=<?= $booking['id'] ?>"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                <a href="bookings.php" class="text-sm text-orange-600 hover:text-orange-700">View all bookings</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="manage-rooms.php" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-door-open text-blue-500"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium">Manage Rooms</h4>
                        <p class="text-gray-500 text-sm">Add, edit, or update room status</p>
                    </div>
                </div>
            </a>

            <a href="manage-room-types.php" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center space-x-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-layer-group text-purple-500"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium">Room Types</h4>
                        <p class="text-gray-500 text-sm">Manage your hostel's room categories</p>
                    </div>
                </div>
            </a>

            <a href="update-hostel.php" class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center space-x-4">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-building text-orange-500"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium">Update Hostel</h4>
                        <p class="text-gray-500 text-sm">Edit your hostel details and information</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>