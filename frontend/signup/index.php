<?php
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $checkQuery = "SELECT id FROM student WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "Email already exists!";
    } else {
        // Insert new student
        $query = "INSERT INTO student (firstName, lastName, email, phone, gender, password)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt->execute([$firstName, $lastName, $email, $phone, $gender, $password])) {
            header("Location: login.php"); // Redirect to login
            exit();
        } else {
            echo "Signup failed!";
        }
    }
}
?>
<form method="post">
    <input type="text" name="firstName" placeholder="First Name" required>
    <input type="text" name="lastName" placeholder="Last Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <select name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
</form>