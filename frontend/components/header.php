<!-- Header -->
<?php 
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
?>

<header class="bg-white px-3 pt-3 pb-2 sticky top-0 z-50">
    <div class="flex items-center justify-center">
        <!-- Logo Image -->
        <img src="<?php echo './images/room.png' ?>" alt="Room Vibe Logo" class="w-10 h-10 mr-2">
        <h1 class="text-xl font-semibold text-orange-500">Room Vibe</h1>
    </div>
    <div class="mt-2">
        <input type="text" id="search" oninput="filterHostels()" placeholder=" Search hostels..."
            class="w-full p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
</header>