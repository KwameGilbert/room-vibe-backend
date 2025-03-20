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
    <header class="bg-white shadow p-4">
        <h1 class="text-2xl font-bold text-center">Room Vibe</h1>
        <div class="mt-2">
            <input type="text" id="search" placeholder="Search hostels..." class="w-full p-2 border rounded">
        </div>
    </header>

    <!-- Filters -->
    <?php include './components/filters.php'; ?>

    <!-- Main Content -->
    <main>
        <?php include './components/hostelListingComponent.php'; ?>
    </main>

    <!-- Navbar -->
    <?php 
    $currentPage = 'explore';
    include './components/navbar.php';
    ?>

</body>

</html>