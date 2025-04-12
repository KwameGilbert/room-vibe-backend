<?php
session_start();
require_once '../config/Database.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['manager_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}

$error = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Connect to database
        $database = new Database();
        $conn = $database->getConnection();
        
        // Query manager
        $stmt = $conn->prepare("SELECT id, name, email, password FROM manager WHERE email = ?");
        $stmt->execute([$email]);
        $manager = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify credentials
        if ($manager && password_verify($password, $manager['password'])) {
            // Set session
            $_SESSION['manager_id'] = $manager['id'];
            $_SESSION['manager_name'] = $manager['name'];
            $_SESSION['manager_email'] = $manager['email'];
            
            header('Location: pages/dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Please enter email and password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login | RoomVibe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-orange-500">RoomVibe</h1>
                <p class="text-gray-600 mt-2">Hostel Manager Portal</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Sign In
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="#" class="text-orange-600 hover:text-orange-500">
                    Forgot your password?
                </a>
            </div>
        </div>
    </div>
</body>

</html>