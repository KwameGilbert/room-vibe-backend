<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-md lg:hidden z-50 px-3">
    <ul class="flex justify-between items-center w-full h-full py-2 px-2">
        <!-- Explore -->
        <li class="text-gray-500" id="explore">
            <a class="flex flex-col items-center" href="/explore">
                <i class="far fa-compass text-2xl text-gray-500"></i>
                <p class="text-[10px] font-semibold text-gray-500">Explore</p>
            </a>
        </li>
        <!-- Wishlist -->
        <li class="text-gray-500" id="wishlist">
            <a class="flex flex-col items-center" href="/wishlist">
                <i class="far fa-star text-2xl text-gray-500"></i>
                <p class="text-[10px] font-semibold text-gray-500">Wishlist</p>
            </a>
        </li>
        <!-- Map -->
        <li class="text-gray-500" id="map">
            <a class="flex flex-col items-center" href="/map">
                <i class="far fa-map text-2xl text-gray-500"></i>
                <p class="text-[10px] font-semibold text-gray-500">Map</p>
            </a>
        </li>
        <!-- Bookings -->
        <li class="text-gray-500" id="booking">
            <a class="flex flex-col items-center" href="/booking">
                <i class="far fa-bookmark text-2xl text-gray-500"></i>
                <p class="text-[10px] font-semibold text-gray-500">Bookings</p>
            </a>
        </li>
        <!-- Profile -->
        <li class="text-gray-500" id="profile">
            <a class="flex flex-col items-center" href="/profile">
                <i class="far fa-user text-2xl text-gray-500"></i>
                <p class="text-[10px] font-semibold text-gray-500">Profile</p>
            </a>
        </li>
    </ul>
</nav>

<script>
// Define the active color (orange-like) and the default inactive color (gray).
const activeColor = "#fd7e14";

// Wait for the DOM to load before applying the active class
document.addEventListener('DOMContentLoaded', () => {
    // Get the element corresponding to the current page by its id.
    const activeItem = document.getElementById(currentPage);
    if (activeItem) {
        // Change the text color for the <li> element itself.
        activeItem.style.color = activeColor;

        // Update the icon color.
        const icon = activeItem.querySelector('i');
        if (icon) {
            icon.style.color = activeColor;
        }

        // Update the text color for the <p> element.
        const text = activeItem.querySelector('p');
        if (text) {
            text.style.color = activeColor;
        }
    }
});
</script>