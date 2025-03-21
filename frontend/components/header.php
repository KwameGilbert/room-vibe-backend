<!-- Header -->
<header class="bg-white px-3 pt-3 pb-2 sticky top-0 z-50">
    <div class="flex items-center justify-center">
        <!-- Logo Image -->
        <img src="https://cdn-icons-png.flaticon.com/512/1946/1946488.png" alt="Room Vibe Logo" class="w-5 h-5 mr-2">
        <h1 class="text-xl font-semibold text-orange-500">Room Vibe</h1>
    </div>
    <div class="mt-2">
        <input type="text" id="search" placeholder="Search hostels..."
            class="w-full p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search");
    const hostelList = document.getElementById("hostel-list");
    const hostels = hostelList.getElementsByClassName(
        "w-full flex gap-4 border-b border-t border-slate-300 py-5");

    searchInput.addEventListener("input", function() {
        const query = this.value.trim().toLowerCase();

        Array.from(hostels).forEach(hostel => {
            const name = hostel.querySelector("h1").textContent.toLowerCase();
            const school = hostel.querySelector("p").textContent.toLowerCase();

            // Show or hide based on search query
            if (name.includes(query) || school.includes(query)) {
                hostel.style.display = "flex"; // Show
            } else {
                hostel.style.display = "none"; // Hide
            }

            // Check if all hostels are hidden
            const visibleHostels = Array.from(hostels).filter(h => h.style.display !== "none");
            const noResultsMsg = document.getElementById("no-results") || (() => {
                const msg = document.createElement("div");
                msg.id = "no-results";
                msg.className = "text-center py-8 text-gray-500";
                msg.textContent = "No hostels found";
                hostelList.appendChild(msg);
                return msg;
            })();

            noResultsMsg.style.display = visibleHostels.length === 0 ? "block" : "none";
        });
    });
});
</script>