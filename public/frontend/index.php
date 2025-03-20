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
    <!-- Header -->
    <?php include './components/header.php'; ?>

    <!-- Filters -->
    <?php include './components/filters.php'; ?>

    <!-- Main Content -->
    <main>
        <?php include './components/hostelListingComponent.php'; ?>
    </main>

    <!-- Navbar -->
    <script>
        // Set the current page name. You can derive this from window.location.pathname if needed.
        const currentPage = "explore"; // Example: "explore", "wishlist", "map", "booking", "profile"
    </script>
    <?php
    include './components/navbar.php';
    ?>

</body>

</html>