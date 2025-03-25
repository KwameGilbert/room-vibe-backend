<?php
session_start();
include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if (!isset($_SESSION['student_id'])) {
    header("Location: " . __DIR__ . "/../splash.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    die("Student not found.");
}

// Fetch statistics (adjust table names and queries to your actual schema)
$stmt = $conn->prepare("SELECT COUNT(*) as bookings FROM booking WHERE student_id = ?");
$stmt->execute([$student_id]);
$bookings = $stmt->fetch(PDO::FETCH_ASSOC)['bookings'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as reviews FROM review WHERE student_id = ?");
$stmt->execute([$student_id]);
$reviews = $stmt->fetch(PDO::FETCH_ASSOC)['reviews'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as wishlist FROM wishlist WHERE student_id = ?");
$stmt->execute([$student_id]);
$wishlist = $stmt->fetch(PDO::FETCH_ASSOC)['wishlist'] ?? 0;

// // Set default profile picture if none is set
// $profilePicture = $student['profile_picture'] ?: "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix";
$profilePicture = "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix";

// For demo: assume the student record has a 'verified' field
$verified = $student['verified'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en" class="h-full w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomVibe Profile</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tailwind CSS -->

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    'poppins': ['Poppins', 'sans-serif']
                },
                colors: {
                    'primary': '#fd7e14',
                    'yellow': '#fbbf24',
                    /* using Tailwind yellow or your custom value */
                    'graycustom': '#4a4a4a'
                }
            }
        }
    }
    </script>
</head>

<body class="h-full w-full overflow-auto bg-gray-100 text-gray-800 font-poppins flex flex-col">
    <!-- Notification Container (for messages sliding from the top) -->
    <div id="notification"
        class="fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50">
    </div>

    <!-- Profile Header -->
    <header class="bg-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-2 flex items-center">
            <!-- Student Profile Picture -->
            <div class="relative">
                <img class="w-16 h-16 rounded-full object-cover border-2 border-yellow-500"
                    src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" />
                <span class="absolute bottom-0 right-0 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white"></span>
            </div>
            <!-- Name and Email -->
            <div class="ml-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <?php echo htmlspecialchars($student['firstName']), " ", htmlspecialchars($student['lastName']); ?>
                </h2>
                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($student['email']); ?></p>
                <?php if (!empty($student['verified']) && $student['verified'] == 1): ?>
                <span class="text-xs bg-green-100 text-yellow-700 px-2 py-1 rounded-full mt-1 inline-block">
                    <i class="fas fa-check-circle mr-1"></i>Verified Student
                </span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Promotional Card -->
    <section class="max-w-4xl mx-auto px-4 mt-6">
        <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 shadow rounded-lg p-6 text-center">
            <h1 class="text-3xl font-bold text-white">RoomVibe</h1>
            <p class="mt-2 text-white text-opacity-90">
                Hostel bookings and accommodation just got easier on your campus.
            </p>
            <button
                class="mt-4 bg-white text-yellow-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-medium transition duration-200 shadow-sm">
                Book Now
            </button>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="max-w-4xl mx-auto px-4 mt-6">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-3xl font-bold text-yellow-500"><?php echo $bookings; ?></p>
                <p class="text-sm text-gray-600">Bookings</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-3xl font-bold text-yellow-500"><?php echo $reviews; ?></p>
                <p class="text-sm text-gray-600">Reviews</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-3xl font-bold text-yellow-500"><?php echo $wishlist; ?></p>
                <p class="text-sm text-gray-600">Wishlist</p>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <nav class="max-w-4xl mx-auto px-4 mt-6 mb-24">
        <ul class="bg-white shadow rounded-lg divide-y divide-gray-200">
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('personal-info')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-user-circle text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Personal Info</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('booking-history')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-history text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Booking History</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('payment-methods')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-credit-card text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Payment Methods</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('referral-program')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-gift text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Referral Program</span>
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">New</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('contact-us')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-envelope text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Contact Us</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" onclick="loadPage('help-support')">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-question-circle text-yellow-500"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Help & Support</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
                <div class="flex justify-between items-center" id="logout-btn">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-gray-600"></i>
                        </div>
                        <span class="text-gray-800 font-medium ml-3">Logout</span>
                    </div>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-sm">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Confirm Logout</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-4">
                <button id="cancelLogout"
                    class="px-4 py-2 rounded-lg text-gray-800 bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
                <button id="confirmLogout"
                    class="px-4 py-2 rounded-lg text-white bg-[#fbbf24] hover:bg-primary-dark transition">Logout</button>
            </div>
        </div>
    </div>



    <script>
    // Replace the old logout listener with this new modal-based approach
    document.getElementById('logout-btn').addEventListener('click', function() {
        // Show the logout confirmation modal
        document.getElementById('logoutModal').classList.remove('hidden');
    });

    // Handle cancel action
    document.getElementById('cancelLogout').addEventListener('click', function() {
        document.getElementById('logoutModal').classList.add('hidden');
    });

    // Handle confirm logout action
    document.getElementById('confirmLogout').addEventListener('click', function() {
        // Use AJAX to call logout.php
        fetch('./login/logout.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Logged out successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = './splash.php';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Logout failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                showNotification('Connection error. Please check your internet connection.', 'error');
            });
    });

    function showNotification(message, type) {
        const notificationContainer = document.getElementById('notification');
        const notification = document.createElement('div');
        notification.className = type === 'success' ?
            'bg-green-500 text-white p-4 shadow-lg z-100' :
            'bg-red-500 text-white p-4 shadow-lg z-100';
        notification.innerHTML = `
                <div class="max-w-md mx-auto flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="${type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'} mr-3"></i>
                        <p>${message}</p>
                    </div>
                    <button class="text-white focus:outline-none" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        notificationContainer.innerHTML = '';
        notificationContainer.appendChild(notification);
        setTimeout(() => {
            notificationContainer.classList.add('translate-y-0');
            notificationContainer.classList.remove('-translate-y-full');
        }, 100);
        setTimeout(() => {
            notificationContainer.classList.remove('translate-y-0');
            notificationContainer.classList.add('-translate-y-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    // showNotification('Profile loaded successfully!', 'success');
    </script>