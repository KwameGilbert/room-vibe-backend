<?php

?>
<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-md lg:hidden z-50 px-3">
    <ul class="flex justify-between items-center w-full h-full py-2 px-2">
        <!-- Explore -->
        <li class="<?= ($currentPage === 'explore' ? 'text-[#fd7e14]' : 'text-gray-500') ?>" id="explore">
            <a class="flex flex-col items-center" href="/explore">
                <i
                    class="far fa-compass text-2xl <?= ($currentPage === 'explore' ? 'text-[#fd7e14]' : 'text-black-500') ?>"></i>
                <p
                    class="text-[10px] font-semibold <?= ($currentPage === 'explore' ? 'text-[#fd7e14]' : 'text-gray-500') ?>">
                    Explore</p>
            </a>
        </li>
        <!-- Wishlist -->
        <li class="<?= ($currentPage === 'wishlist' ? 'text-[#fd7e14]' : 'text-gray-500') ?>" id="wishlist">
            <a class="flex flex-col items-center" href="/wishlist">
                <i
                    class="far fa-star text-2xl <?= ($currentPage === 'wishlist' ? 'text-[#fd7e14]' : 'text-black-500') ?>"></i>
                <p
                    class="text-[10px] font-semibold <?= ($currentPage === 'wishlist' ? 'text-[#fd7e14]' : 'text-gray-500') ?>">
                    Wishlist</p>
            </a>
        </li>
        <!-- Map -->
        <li class="<?= ($currentPage === 'map' ? 'text-[#fd7e14]' : 'text-gray-500') ?>" id="map">
            <a class="flex flex-col items-center" href="/map">
                <i
                    class="far fa-map text-2xl <?= ($currentPage === 'map' ? 'text-[#fd7e14]' : 'text-black-500') ?>"></i>
                <p
                    class="text-[10px] font-semibold <?= ($currentPage === 'map' ? 'text-[#fd7e14]' : 'text-gray-500') ?>">
                    Map</p>
            </a>
        </li>
        <!-- Bookings -->
        <li class="<?= ($currentPage === 'booking' ? 'text-[#fd7e14]' : 'text-gray-500') ?>" id="booking">
            <a class="flex flex-col items-center" href="/booking">
                <i
                    class="far fa-bookmark text-2xl <?= ($currentPage === 'booking' ? 'text-[#fd7e14]' : 'text-black-500') ?>"></i>
                <p
                    class="text-[10px] font-semibold <?= ($currentPage === 'booking' ? 'text-[#fd7e14]' : 'text-gray-500') ?>">
                    Bookings</p>
            </a>
        </li>
        <!-- Profile -->
        <li class="<?= ($currentPage === 'profile' ? 'text-[#fd7e14]' : 'text-gray-500') ?>" id="profile">
            <a class="flex flex-col items-center" href="/profile">
                <i
                    class="far fa-user text-2xl <?= ($currentPage === 'profile' ? 'text-[#fd7e14]' : 'text-black-500') ?>"></i>
                <p
                    class="text-[10px] font-semibold <?= ($currentPage === 'profile' ? 'text-[#fd7e14]' : 'text-gray-500') ?>">
                    Profile</p>
            </a>
        </li>
    </ul>
</nav>