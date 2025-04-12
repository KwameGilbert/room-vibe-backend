<nav class="w-64 bg-white shadow-lg h-screen fixed left-0">
    <div class="p-6 border-b">
        <h1 class="text-2xl font-bold text-orange-500">RoomVibe</h1>
        <p class="text-gray-600 text-sm">Manager Portal</p>
    </div>

    <div class="py-4">
        <div class="px-4 py-2 mb-2">
            <p class="text-gray-500 text-xs uppercase font-semibold">Main</p>
        </div>

        <!-- Dashboard -->
        <a href="dashboard.php" class="block px-4 py-2 <?= $currentPage === 'dashboard.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span>Dashboard</span>
            </div>
        </a>

        <div class="px-4 py-2 mb-2 mt-4">
            <p class="text-gray-500 text-xs uppercase font-semibold">Hostel Management</p>
        </div>

        <!-- Hostel Information -->
        <a href="update-hostel.php"
            class="block px-4 py-2 <?= $currentPage === 'update-hostel.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-building w-6"></i>
                <span>Hostel Details</span>
            </div>
        </a>

        <!-- Room Types -->
        <a href="manage-room-types.php"
            class="block px-4 py-2 <?= $currentPage === 'manage-room-types.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-layer-group w-6"></i>
                <span>Room Types</span>
            </div>
        </a>

        <!-- Rooms -->
        <a href="manage-rooms.php"
            class="block px-4 py-2 <?= $currentPage === 'manage-rooms.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-door-open w-6"></i>
                <span>Rooms</span>
            </div>
        </a>

        <!-- Images -->
        <a href="hostel-images.php"
            class="block px-4 py-2 <?= $currentPage === 'hostel-images.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-images w-6"></i>
                <span>Hostel Images</span>
            </div>
        </a>

        <div class="px-4 py-2 mb-2 mt-4">
            <p class="text-gray-500 text-xs uppercase font-semibold">Bookings</p>
        </div>

        <!-- Bookings List -->
        <a href="bookings.php" class="block px-4 py-2 <?= $currentPage === 'bookings.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-calendar-check w-6"></i>
                <span>Bookings</span>
            </div>
        </a>

        <div class="px-4 py-2 mb-2 mt-4">
            <p class="text-gray-500 text-xs uppercase font-semibold">Reports</p>
        </div>

        <!-- Reports -->
        <a href="reports.php" class="block px-4 py-2 <?= $currentPage === 'reports.php' ? 'sidebar-active' : '' ?>">
            <div class="flex items-center">
                <i class="fas fa-chart-bar w-6"></i>
                <span>Reports</span>
            </div>
        </a>
    </div>

    <!-- User info and logout -->
    <div class="absolute bottom-0 w-full border-t px-4 py-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['manager_name'] ?? 'Manager') ?></p>
                <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['manager_email'] ?? '') ?></p>
            </div>
            <a href="../logout.php" class="text-red-500 hover:text-red-700">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>