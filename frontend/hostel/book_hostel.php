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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    </style>
</head>

<body class="bg-gradient-to-r from-purple-300 via-pink-200 to-blue-200 min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-lg shadow-md z-50">
        <div class="max-w-4xl mx-auto flex items-center justify-between p-4">
            <button onclick="window.history.back()" class="p-2 text-purple-600 hover:text-purple-800 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800">Book Your Stay</h1>
            <div></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto pt-20 px-4">
        <!-- Hostel Info Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($hostel['hostel_name']); ?></h2>
            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($hostel['school_name']); ?></p>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form id="bookingForm">
                <!-- Personal Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="firstName"
                                value="<?php echo htmlspecialchars($student['firstName']); ?>"
                                class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="lastName"
                                value="<?php echo htmlspecialchars($student['lastName']); ?>"
                                class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Academic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                            <input type="text" name="program" required placeholder="Enter your program"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year of Study</label>
                            <select name="yearOfStudy" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-400">
                                <option value="">Select year of study</option>
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Session</label>
                            <select name="session" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-400">
                                <option value="">Select academic session</option>
                                <option value="2024/2025 Fulltime">2024/2025 Fulltime</option>
                                <option value="2025/2026 Fulltime">2025/2026 Fulltime</option>
                                <option value="Sandwich">Sandwich</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"
                                class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>"
                                class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200 shadow-md">
                    Confirm Booking
                </button>
            </form>
        </div>
    </main>
</body>

</html>