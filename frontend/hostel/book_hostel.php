<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: splash.php");
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Get hostel_id from URL
if (!isset($_GET['id'])) {
    die("Hostel ID not specified.");
}
$hostel_id = $_GET['id'];

// Fetch hostel details
$stmt = $conn->prepare("SELECT h.*, s.name as school_name FROM hostel h JOIN school s ON h.school_id = s.id WHERE h.id = ?");
$stmt->execute([$hostel_id]);
$hostel = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$hostel) {
    die("Hostel not found.");
}

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book <?php echo htmlspecialchars($hostel['hostel_name']); ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md shadow-sm z-50 p-4">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <button onclick="window.history.back()" class="p-2 text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800">Book Your Stay</h1>
            <div></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto pt-20 px-4 pb-8">
        <!-- Hostel Info -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow flex">
            <div class="w-1/2 p-4">
                <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($hostel['hostel_name']); ?>
                </h2>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($hostel['school_name']); ?></p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="text-yellow-500 text-sm">★</span>
                    <span class="font-medium text-sm"><?php echo htmlspecialchars($hostel['rating']); ?></span>
                </div>
            </div>
            <img src="<?php echo htmlspecialchars(file_exists("./images/hostels/{$hostel_id}/1.jpg") ? "./images/hostels/{$hostel_id}/1.jpg" : "./images/hostels/default-image.jpg"); ?>"
                alt="Hostel Image" class="w-1/2 h-full object-cover flex-shrink-0 rounded-l-lg">
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg p-6 shadow">
            <form id="bookingForm">
                <!-- Personal Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">First Name</label>
                            <input type="text" name="firstName"
                                value="<?php echo htmlspecialchars($student['firstName']); ?>"
                                class="w-full p-2 border border-gray-300 rounded" readonly>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="lastName"
                                value="<?php echo htmlspecialchars($student['lastName']); ?>"
                                class="w-full p-2 border border-gray-300 rounded" readonly>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Academic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Program</label>
                            <input type="text" name="program" required placeholder="Enter your program"
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Year of Study</label>
                            <select name="yearOfStudy" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">Select year</option>
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Academic Session</label>
                            <select name="session" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">Select session</option>
                                <option value="2024/2025 Fulltime">2024/2025 Fulltime</option>
                                <option value="2025/2026 Fulltime">2025/2026 Fulltime</option>
                                <option value="Sandwich">Sandwich</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"
                                class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>"
                                class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded font-medium transition">
                    Confirm Booking
                </button>
            </form>
        </div>
    </main>
</body>

</html>