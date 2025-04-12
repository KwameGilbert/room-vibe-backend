<header class="bg-white shadow-md h-16 flex items-center fixed top-0 right-0 left-64 z-10">
    <div class="container mx-auto px-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800"><?= $pageTitle ?? 'Dashboard' ?></h2>
        </div>

        <div class="flex items-center space-x-4">
            <div class="relative">
                <button class="text-gray-500 hover:text-orange-500">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
            <div>
                <button id="userMenuButton" class="text-gray-500 hover:text-orange-500">
                    <i class="fas fa-user-circle text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>