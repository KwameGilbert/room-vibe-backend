 <!-- Header -->
 <?php include __DIR__ . '/../components/header.php'; ?>

 <!-- Filters -->
 <?php include __DIR__ . '/../components/filters.php'; ?>

 <!-- Main Content -->
 <main>
     <?php include __DIR__ . '/../components/hostelListingComponent.php'; ?>
 </main>

 <!-- Navbar -->
 <script>
// Set the current page name. You can derive this from window.location.pathname if needed.
const currentPage = "explore"; // Example: "explore", "wishlist", "map", "booking", "profile"
 </script>
 <?php
    include __DIR__ . '/../components/navbar.php';
    ?>