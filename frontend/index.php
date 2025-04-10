<?php
include './config/session_check_student.php';
$student_id = $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Vibe</title>
    <?php include_once __DIR__ . "/includes/links.php"; ?>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Configurations -->
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    'poppins': ['Poppins', 'sans-serif']
                },
                colors: {
                    'primary': #fd7e14,
                    'primary-dark': #e76b00,
                    'secondary': #fbbf24,
                    'baby-powder': #FFFFFC
                }
            }
        }
    }
    </script>
</head>

<body class="bg-gray-100">
    <!-- Page Content -->
    <div id="page-content"></div>

    <?php include './components/navbar.php' ?>

    <script src="./scripts/index.min.js"></script>


</body>

</html>