<?php
// session_start();
// if (!isset($_SESSION['student_id'])) {
// header("Location: ./login/");
// exit();
// }
// index.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Vibe</title>
    <?php include './components/links.php'; ?>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-gray-100">
    <!-- Page Content -->
    <div id="page-content"></div>

    <?php include './components/navbar.php' ?>

    <script src="./js/index.js"></script>

    <script type='text/javascript'>
    document.addEventListener('DOMContentLoaded', function() {
        window.setTimeout(document.querySelector('#storyset').classList.add('animated'), 1000);
    })
    </script>
</body>

</html>