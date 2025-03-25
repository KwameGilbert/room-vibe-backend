<!-- Filter Buttons Container -->
<div class="flex space-x-2 bg-white p-2 pt-1 shadow-md overflow-x-auto">
    <button
        class="filter-btn bg-gray-200 rounded-full px-4 py-1 whitespace-nowrap transform transition duration-200 hover:shadow-lg hover:-translate-y-1"
        data-filter="school">
        School
    </button>
    <button
        class="filter-btn bg-gray-200 rounded-full px-4 py-1 whitespace-nowrap transform transition duration-200 hover:shadow-lg hover:-translate-y-1"
        data-filter="amenities">
        Amenities
    </button>
    <button
        class="filter-btn bg-gray-200 rounded-full px-4 py-1 whitespace-nowrap transform transition duration-200 hover:shadow-lg hover:-translate-y-1"
        data-filter="price">
        Price Range
    </button>
    <button
        class="filter-btn bg-gray-200 rounded-full px-4 py-1 whitespace-nowrap transform transition duration-200 hover:shadow-lg hover:-translate-y-1"
        data-filter="room">
        Room Type
    </button>
</div>

<!-- Modal Template -->
<div id="filterModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-md p-6">
        <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
        <div id="modalContent" class="mb-4">
            <!-- Filter options will be injected here -->
        </div>
        <div class="flex justify-end space-x-2">
            <button id="modalCancel" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 focus:outline-none">
                Cancel
            </button>
            <button id="modalApply"
                class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 focus:outline-none">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- JavaScript to handle modal and filter logic -->
<!-- <script>
document.addEventListener('DOMContentLoaded', () => {
    // All filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    // Modal elements
    const modal = document.getElementById('filterModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    const modalCancel = document.getElementById('modalCancel');
    const modalApply = document.getElementById('modalApply');

    // Example data for each filter type.
    const filterOptions = {
        school: ['School A', 'School B', 'School C'],
        amenities: ['WiFi', 'Laundry', 'Gym', 'Pool'],
        price: ['$0-$100', '$100-$200', '$200-$300', '$300+'],
        room: ['Single', 'Double', 'Suite', 'Dorm']
    };

    let currentFilter = null;
    let selectedOption = null;

    // Show the modal and populate options based on which filter button was clicked.
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            currentFilter = btn.getAttribute('data-filter');
            modalTitle.textContent = 'Select ' + currentFilter.charAt(0).toUpperCase() +
                currentFilter.slice(1);
            modalContent.innerHTML = ''; // Clear previous content
            selectedOption = null; // Reset selection

            // Create a button for each option
            (filterOptions[currentFilter] || []).forEach(option => {
                const optionBtn = document.createElement('button');
                optionBtn.textContent = option;
                optionBtn.className =
                    "w-full text-left px-4 py-2 border rounded mb-2 hover:bg-gray-100 focus:outline-none";
                optionBtn.addEventListener('click', () => {
                    // Remove highlight from all options and highlight the selected one
                    modalContent.querySelectorAll('button').forEach(b => b
                        .classList.remove('bg-orange-100'));
                    optionBtn.classList.add('bg-orange-100');
                    selectedOption = option;
                });
                modalContent.appendChild(optionBtn);
            });
            modal.classList.remove('hidden');
        });
    });

    // Hide modal on cancel
    modalCancel.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // On apply, trigger the filter fetch (AJAX) and then close the modal.
    modalApply.addEventListener('click', () => {
        // For demonstration, log the selection
        console.log('Filter Applied:', currentFilter, selectedOption);

        // Example AJAX fetch (adjust endpoint and query parameters as needed)
        /*
        fetch(`/api/hostels?filter=${currentFilter}&value=${encodeURIComponent(selectedOption)}`)
          .then(response => response.json())
          .then(data => {
              // Update your hostel listing dynamically
              console.log(data);
          })
          .catch(err => console.error(err));
        */
        modal.classList.add('hidden');
    });
});
</script> -->