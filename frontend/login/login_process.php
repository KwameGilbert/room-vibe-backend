<?php
//login_process.php

require_once __DIR__ . '/../config/Database.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $conn = $database->getConnection();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, password FROM student WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $response['message'] = "No account found with this email address.";
    } else {
        if (password_verify($password, $student['password'])) {
            session_start();
            $_SESSION['student_id'] = $student['id'];
            $response['success'] = true;
        } else {
            $response['message'] = "Incorrect password.";
        }
    }
}

header("Content-Type: application/json");
echo json_encode($response);