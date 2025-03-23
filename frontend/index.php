<?php
session_start();
// if (!isset($_SESSION['student_id'])) {
// Get the current student ID (assumed to be stored in the session)
// header("Location: ./login/");
// exit();
// }
// index.php
$_SESSION['student_id'] = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Vibe</title>
    <?php include './components/links.php'; ?>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
</head>

<body class="bg-gray-100">
    <!-- Page Content -->
    <div id="page-content"></div>

    <?php include './components/navbar.php' ?>

    <script src="./js/index.min.js"></script>
    <script>
    // // Check if Service Workers are supported
    // if ('serviceWorker' in navigator) {
    //     window.addEventListener('load', () => {
    //         navigator.serviceWorker.register('./service-worker.min.js').then((registration) => {
    //             console.log('Service Worker registered successfully with scope: ', registration.scope);
    //         }).catch((error) => {
    //             console.log('Service Worker registration failed: ', error);
    //         });
    //     });
    // }
    </script>
</body>

</html>