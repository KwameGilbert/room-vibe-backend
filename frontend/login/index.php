<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, password FROM students WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        header("Location: " . __DIR__ . "/../");
        exit();
    } else {
        echo "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Room Vibe</title>
    <!-- Tailwind CSS CDN (or your custom build) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-r from-gray-100 to-white flex items-center justify-center min-h-screen">
    <div
        class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full transform hover:scale-105 transition-transform duration-300">
        <div class="flex justify-center mb-4">
            <!-- Cartoon avatar image; replace with your desired cartoon image URL -->
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cartoon Avatar"
                class="w-20 h-20 rounded-full shadow-md">
        </div>
        <h2 class="text-center text-2xl font-bold text-orange-500 mb-6">Welcome Back!</h2>
        <form method="post" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required
                    class="w-full p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required
                    class="w-full p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <button type="submit"
                class="w-full bg-orange-500 text-white p-3 rounded-full shadow-lg hover:bg-orange-600 transition-colors duration-200">
                Login
            </button>
        </form>
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>Don't have an account? <a href="signup.php" class="text-orange-500 font-semibold">Sign Up</a></p>
        </div>
    </div>
</body>

</html>