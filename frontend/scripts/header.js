
document.addEventListener('DOMContentLoaded', () => {
    function filterHostels(query) {
        const hostelItems = document.querySelectorAll('#hostel-list > div');
        hostelItems.forEach(item => {
            const name = item.querySelector('h2').textContent.toLowerCase();
            if (name.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    };

    document.getElementById('search').addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase();
        filterHostels(query);
    });
});

