 <!-- Header -->
 <?php include __DIR__ . '/../components/header.php'; ?>

 <!-- Filters -->
 <?php include __DIR__ . '/../components/filters.php'; ?>

 <!-- Main Content -->
 <?php include __DIR__ . '/../components/hostelListingComponent.php'; ?>
 <script>
function filterHostels() {
    const searchInput = document.getElementById("search");
    const
        hostelList = document.getElementById("hostel-list");
    const
        hostels = hostelList.getElementsByClassName("w-full flex gap-4 border-b border-t border-slate-300 py-5");
    const
        query = searchInput.value.trim().toLowerCase();
    Array.from(hostels).forEach(hostel => {
        const name = hostel.querySelector("h1").textContent.toLowerCase();
        const school = hostel.querySelector("p").textContent.toLowerCase();

        // Show or hide based on search query
        if (name.includes(query) || school.includes(query)) {
            hostel.style.display = "flex"; // Show
        } else {
            hostel.style.display = "none"; // Hide
        }
    });

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
}
 </script>