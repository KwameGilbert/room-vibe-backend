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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($hostel['hostel_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 px-4 py-3">
        <div class="max-w-2xl mx-auto flex items-center">
            <button onclick="window.history.back()"
                class="flex items-center justify-center w-10 rounded transition-colors">
                <i class="fas fa-arrow-left text-primary"></i>
            </button>
            <h1 class="text-lg font-semibold text-secondary">Book Your Stay</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto pt-16 px-4 pb-20">
        <!-- Hostel Info -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <h2 class="font-semibold text-lg"><?php echo htmlspecialchars($hostel['hostel_name']); ?></h2>
            <p class="text-gray-600"><?php echo htmlspecialchars($hostel['school_name']); ?></p>
        </div>

        <!-- Booking Form -->
        <form id="bookingForm" class="bg-white rounded-lg shadow-sm p-4">
            <div class="space-y-4">
                <!-- Personal Information -->
                <div>
                    <h3 class="font-semibold mb-3">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="firstName"
                                value="<?php echo htmlspecialchars($student['firstName']); ?>"
                                class="w-full p-2 border rounded-lg" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="lastName"
                                value="<?php echo htmlspecialchars($student['lastName']); ?>"
                                class="w-full p-2 border rounded-lg" readonly>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div>
                    <h3 class="font-semibold mb-3">Academic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                            <input type="text" name="program" required placeholder="Enter your program"
                                class="w-full p-2 border rounded-lg focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year of Study</label>
                            <select name="yearOfStudy" required
                                cls="w-full p-2 border rounded-lg focus:ring-primary focus:border-primary">
                                <option value="">Select year of study</option>
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Session</label>
                            <select name="session" required
                                class="w-full p-2 border rounded-lg focus:ring-primary focus:border-primary">
                                <option value="">Select academic session</option>
                                <option value="2024/2025 Fulltime">2024/2025 Fulltime</option>
                                <option value="2025/2026 Fulltime">2025/2026 Fulltime</option>
                                <option value="Sandwich">Sandwich</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="font-semibold mb-3">Contact Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"
                                class="w-full p-2 border rounded-lg" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>"
                                class="w-full p-2 border rounded-lg" readonly>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-primary hover:bg-orange-600 text-white font-bold py-4 px-4 rounded-lg transition duration-200">
                    Confirm Booking
                </button>
            </div>
        </form>
    </main>
</body>

</html>