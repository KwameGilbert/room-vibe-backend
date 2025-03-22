<!-- Profile Page for RoomVibe Student Hostel App -->

<!-- Profile Header -->
<header class="bg-white shadow">
    <div class="max-w-4xl mx-auto px-4 py-6 flex items-center">
        <!-- Student Profile Picture -->
        <div class="relative">
            <img class="w-16 h-16 rounded-full object-cover border-2 border-yellow-500"
                src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="Profile Picture" />
            <span class="absolute bottom-0 right-0 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white"></span>
        </div>
        <!-- Name and Email -->
        <div class="ml-4">
            <h2 class="text-xl font-bold text-gray-800">John Doe</h2>
            <p class="text-gray-600 text-sm">john.doe@university.edu</p>
            <span class="text-xs bg-green-100 text-yellow-700 px-2 py-1 rounded-full mt-1 inline-block">
                <i class="fas fa-check-circle mr-1"></i>Verified Student
            </span>
        </div>
    </div>
</header>

<!-- Promotional Card -->
<section class="max-w-4xl mx-auto px-4 mt-6">
    <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 shadow rounded-lg p-6 text-center">
        <h1 class="text-3xl font-bold text-white">RoomVibe</h1>
        <p class="mt-2 text-white text-opacity-90">
            Hostel bookings and accommodation just got easier on your campus.
        </p>
        <button
            class="mt-4 bg-white text-yellow-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-medium transition duration-200 shadow-sm">
            Book Now
        </button>
    </div>
</section>

<!-- Stats Section -->
<section class="max-w-4xl mx-auto px-4 mt-6">
    <div class="grid grid-cols-3 gap-4 text-center">
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-3xl font-bold text-yellow-500">2</p>
            <p class="text-sm text-gray-600">Bookings</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-3xl font-bold text-yellow-500">5</p>
            <p class="text-sm text-gray-600">Reviews</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-3xl font-bold text-yellow-500">3</p>
            <p class="text-sm text-gray-600">Wishlist</p>
        </div>
    </div>
</section>

<!-- Menu Section -->
<nav class="max-w-4xl mx-auto px-4 mt-6 mb-24">
    <ul class="bg-white shadow rounded-lg divide-y divide-gray-200">
        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('personal-info')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-user-circle text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Personal Info</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('booking-history')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-history text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Booking History</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('payment-methods')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-credit-card text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Payment Methods</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('referral-program')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-gift text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Referral Program</span>
                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">New</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('contact-us')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-envelope text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Contact Us</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" onclick="loadPage('help-support')">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-question-circle text-yellow-500"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Help & Support</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </li>

        <li class="p-4 hover:bg-gray-50 cursor-pointer transition duration-200">
            <div class="flex justify-between items-center" id="logout-btn">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-gray-600"></i>
                    </div>
                    <span class="text-gray-800 font-medium ml-3">Logout</span>
                </div>
            </div>
        </li>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    document.getElementById('logout-btn').addEventListener('click', function() {
        if (confirm('Are you sure you want to logout?')) {
            // Send logout request to server
            window.location.href = './login/';
        }
    });

    // Make menu items clickable
    const menuItems = document.querySelectorAll('[onclick^="loadPage"]');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const page = this.getAttribute('onclick').replace('loadPage(\'', '').replace('\')',
                '');
            window.loadPage(page);
        });
    });
});
</script>