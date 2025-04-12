<!-- Header -->
<?php include __DIR__ . '/../components/header.php'; ?>

<!-- Filters -->
<?php include __DIR__ . '/../components/filters.php'; ?>

<!-- Main Content -->
<?php include __DIR__ . '/../components/hostelListingComponent.php'; ?>
<script>
function filterHostels() {
    const searchInput = document.getElementById("search");
    const hostelList = document.getElementById("hostel-list");
    const hostels = hostelList.getElementsByClassName("hostel-item"); // Update class name
    const query = searchInput.value.trim().toLowerCase();

    Array.from(hostels).forEach(hostel => {
        const name = hostel.querySelector(".hostel-name").textContent.toLowerCase();
        const location = hostel.querySelector(".hostel-location").textContent.toLowerCase();

        if (name.includes(query) || location.includes(query)) {
            hostel.parentElement.style.display = "block";
        } else {
            hostel.parentElement.style.display = "none";
        }
    });

    // Check if any hostels are visible
    const visibleHostels = Array.from(hostels).filter(h => h.parentElement.style.display !== "none");
    const noResultsMsg = document.getElementById("no-results") || createNoResultsElement(hostelList);
    noResultsMsg.style.display = visibleHostels.length === 0 ? "block" : "none";
}

function createNoResultsElement(container) {
    const msg = document.createElement("div");
    msg.id = "no-results";
    msg.className = "text-center py-8 text-gray-500 w-full";
    msg.textContent = "No hostels found";
    container.appendChild(msg);
    return msg;
}
</script>