document.getElementById('paystackButton').addEventListener('click', function () {
    const bookingForm = document.getElementById('bookingForm');
    const formData = new FormData(bookingForm);

    // Validate required fields
    const requiredFields = ['roomType', 'room', 'program', 'yearOfStudy', 'session',
        'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone'];
    let isValid = true;

    requiredFields.forEach(field => {
        const element = bookingForm.elements[field];
        if (!element.value.trim()) {
            isValid = false;
            alert(`${field} is required`);
        }
    });

    if (!isValid) return;

    // Paystack payment initialization
    const handler = PaystackPop.setup({
        key: 'your-public-key', // Replace with your Paystack public key
        email: formData.get('email'),
        amount: parseFloat(document.getElementById('roomPrice').textContent.replace('GHS ', '')) * 100, // Convert to kobo
        currency: 'GHS',
        callback: function (response) {
            // Payment successful
            alert('Payment successful. Transaction reference: ' + response.reference);

            // Submit the form data to the server to update booking as paid
            fetch('process_booking.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking confirmed!');
                        window.location.href = 'booking_confirmation.php?id=' + data.booking_id;
                    } else {
                        alert('Failed to confirm booking: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('An error occurred while confirming booking.');
                });
        },
        onClose: function () {
            alert('Payment process was canceled.');
        }
    });

    handler.openIframe();
});