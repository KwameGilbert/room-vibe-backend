<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./../splash.php");
    exit();
}

include_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// Get hostel_id from URL
if (!isset($_GET['id'])) {
    die("Hostel ID not specified.");
}
$hostel_id = $_GET['id'];

// Fetch hostel details
$stmt = $conn->prepare("SELECT h.*, s.name as school_name FROM hostel h JOIN school s ON h.school_id = s.id WHERE h.id = ?");
$stmt->execute([$hostel_id]);
$hostel = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$hostel) {
    die("Hostel not found.");
}

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book <?php echo htmlspecialchars($hostel['hostel_name']); ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- Paystack Inline Script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.7) url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" stroke="%23EAB308" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" opacity="1"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></path></svg>') center/30px no-repeat;
    }

    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .success-message {
        color: #059669;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-group.error input,
    .form-group.error select {
        border-color: #dc2626;
    }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white/70 backdrop-blur-md shadow-sm z-50 p-4">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <button onclick="window.history.back()" class="p-2 text-yellow-500 hover:text-yellow-800">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800">Book Your Stay</h1>
            <div></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto pt-20 px-4 pb-8">
        <!-- Hostel Info -->
        <div class="w-full flex gap-2 border border-slate-300 rounded-lg bg-white p-4 mb-4 shadow">
            <img src="<?php echo htmlspecialchars(file_exists("./images/hostels/{$hostel_id}/1.jpg") ? "./images/hostels/{$hostel_id}/1.jpg" : "./images/hostels/default-image.jpg"); ?>"
                alt="<?= htmlspecialchars($hostel['hostel_name']) ?>"
                class="w-[150px] h-[100px] rounded-lg object-cover">
            <div class="w-full pr-4">
                <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($hostel['hostel_name']); ?></h2>
                <p class="text-sm text-gray-500 font-semibold">
                    <?= htmlspecialchars($hostel['school_name']) ?> - <?= htmlspecialchars($hostel['location']) ?>
                </p>
                <!-- Rating and distance -->
                <div class="flex items-center justify-between gap-3 pt-1">
                    <div class="flex">
                        <?php
                        $rating = isset($hostel['rating']) ? floor($hostel['rating']) : 0;
                        for ($i = 0; $i < 5; $i++):
                            if ($i < $rating):
                        ?>
                        <i class="fas fa-star text-yellow-500"></i>
                        <?php else: ?>
                        <i class="far fa-star text-yellow-500"></i>
                        <?php endif; endfor; ?>
                    </div>
                    <div class="flex items-center text-gray-500 text-sm">
                        <i class="fas fa-walking mr-1"></i>
                        <?= htmlspecialchars($hostel['distance'] ?? 'N/A') ?> km
                    </div>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <button
                        class="<?= ($hostel['accomodation_status'] == 1) ? 'bg-green-500' : 'bg-red-500' ?> px-2 text-white font-semibold rounded-md">
                        <?= ($hostel['accomodation_status'] == 1) ? 'Available' : 'Full' ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Room Selection -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Room Selection</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="block text-sm text-gray-700 mb-1">Room Type</label>
                    <select name="roomType" id="roomType" required
                        class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select room type</option>
                        <?php
                        // Fetch distinct room types available in this hostel
                        $roomTypeStmt = $conn->prepare("
                            SELECT DISTINCT rt.room_type_id, rt.type_name
                            FROM room_types rt
                            JOIN room r ON rt.room_type_id = r.room_type_id
                            WHERE r.hostel_id = ? AND r.price IS NOT NULL
                        ");
                        $roomTypeStmt->execute([$hostel_id]);
                        $roomTypes = $roomTypeStmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roomTypes as $roomType):
                        ?>
                        <option value="<?= htmlspecialchars($roomType['room_type_id']); ?>">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $roomType['type_name']))); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="block text-sm text-gray-700 mb-1">Room Number</label>
                    <select name="room_id" id="room_id" required
                        class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select room type first</option>
                    </select>
                </div>
            </div>
            <!-- Price Display -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Room Price:</span>
                    <span id="roomPrice" class="text-lg font-semibold text-primary" data-price="0">-</span>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg p-6 shadow">
            <form id="bookingForm">
                <!-- Personal Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="firstName" class="block text-sm text-gray-700 mb-1">First Name</label>
                            <input type="text" id="firstName" name="firstName"
                                value="<?php echo htmlspecialchars($student['firstName']); ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="form-group">
                            <label for="lastName" class="block text-sm text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="lastName" name="lastName"
                                value="<?php echo htmlspecialchars($student['lastName']); ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Academic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="program" class="block text-sm text-gray-700 mb-1">Program</label>
                            <input type="text" id="program" name="program" required placeholder="Enter your program"
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div class="form-group">
                            <label for="yearOfStudy" class="block text-sm text-gray-700 mb-1">Year of Study</label>
                            <select id="yearOfStudy" name="yearOfStudy" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select year</option>
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                        </div>
                        <div class="form-group sm:col-span-2">
                            <label for="session" class="block text-sm text-gray-700 mb-1">Academic Session</label>
                            <select id="session" name="session" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select session</option>
                                <option value="2024/2025 Fulltime">2024/2025 Fulltime</option>
                                <option value="2025/2026 Fulltime">2025/2026 Fulltime</option>
                                <option value="Sandwich">Sandwich</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="email" class="block text-sm text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($student['email']); ?>"
                                class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="block text-sm text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($student['phone']); ?>"
                                class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Emergency Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="emergency_contact_name" class="block text-sm text-gray-700 mb-1">Full
                                Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                placeholder="Enter emergency contact name">
                        </div>
                        <div class="form-group">
                            <label for="emergency_contact_relationship"
                                class="block text-sm text-gray-700 mb-1">Relationship</label>
                            <select id="emergency_contact_relationship" name="emergency_contact_relationship" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select relationship</option>
                                <option value="Parent">Parent</option>
                                <option value="Guardian">Guardian</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group sm:col-span-2">
                            <label for="emergency_contact_phone" class="block text-sm text-gray-700 mb-1">Contact
                                Number</label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                placeholder="Enter emergency contact number">
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Additional Notes</h3>
                    <div class="form-group">
                        <label for="notes" class="block text-sm text-gray-700 mb-1">Special Requests (Optional)</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                            placeholder="Any special requests or additional information for your booking"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Maximum 200 characters</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitButton"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white p-3 rounded font-medium transition">
                    Confirm Booking
                </button>
            </form>
        </div>
    </main>

    <!-- JavaScript for Room Fetch & Payment Integration -->
    <script>
    const roomTypeSelect = document.getElementById('roomType');
    const roomSelect = document.getElementById('room_id');
    const priceDisplay = document.getElementById('roomPrice');
    const notes = document.getElementById('notes');
    const bookingForm = document.getElementById('bookingForm');
    const submitButton = document.getElementById('submitButton');

    // Loading, error, and clear functions
    function showLoading(element) {
        element.classList.add('loading');
        if (element === submitButton) {
            element.disabled = true;
        }
    }

    function hideLoading(element) {
        element.classList.remove('loading');
        if (element === submitButton) {
            element.disabled = false;
        }
    }

    function showError(element, message) {
        clearErrorsFor(element);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
        element.parentNode.classList.add('error');
    }

    function clearErrorsFor(element) {
        const parent = element.parentNode;
        const existingErrors = parent.querySelectorAll('.error-message');
        existingErrors.forEach(el => el.remove());
        parent.classList.remove('error');
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.form-group.error').forEach(el => el.classList.remove('error'));
    }

    // Show toast notification using SweetAlert
    function showToast(title, icon = 'info') {
        Swal.fire({
            title: title,
            icon: icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    // Fetch rooms for selected room type
    async function fetchRooms(roomTypeId) {
        showLoading(roomSelect.parentNode);
        try {
            const response = await fetch(`getRooms.php?roomTypeId=${roomTypeId}&hostelId=<?= $hostel_id; ?>`);
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch rooms');
            }

            roomSelect.innerHTML = '<option value="">Select room</option>';
            if (data.rooms.length === 0) {
                throw new Error('No rooms available for this type');
            }

            data.rooms.forEach(room => {
                roomSelect.innerHTML += `
                    <option value="${room.id}" data-price="${room.price}" data-room_number="${room.room_number}">
                        Room ${room.room_number} ${room.specification ? '- ' + room.specification : ''}
                    </option>`;
            });
        } catch (error) {
            console.error('Error:', error);
            showError(roomSelect, error.message);
            roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
        } finally {
            hideLoading(roomSelect.parentNode);
        }
    }

    // Room type change handler
    roomTypeSelect.addEventListener('change', async function() {
        clearErrors();
        priceDisplay.textContent = '-';
        priceDisplay.setAttribute('data-price', '0');
        if (this.value) {
            await fetchRooms(this.value);
        } else {
            roomSelect.innerHTML = '<option value="">Select room type first</option>';
        }
    });

    // Room selection change handler (update price)
    roomSelect.addEventListener('change', function() {
        const selectedRoom = this.options[this.selectedIndex];
        if (selectedRoom && selectedRoom.value) {
            const price = selectedRoom.getAttribute('data-price');
            if (price) {
                const formattedPrice = parseFloat(price).toFixed(2);
                priceDisplay.textContent = `GHS ${formattedPrice}`;
                priceDisplay.setAttribute('data-price', formattedPrice);
            } else {
                priceDisplay.textContent = '-';
                priceDisplay.setAttribute('data-price', '0');
            }
        } else {
            priceDisplay.textContent = '-';
            priceDisplay.setAttribute('data-price', '0');
        }
    });

    // Booking form submission with Paystack integration
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        // Validate required fields
        let isValid = true;
        const requiredFields = ['roomType', 'room_id', 'program', 'yearOfStudy', 'session',
            'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone'
        ];

        requiredFields.forEach(field => {
            const element = this.elements[field];
            if (element && !element.value.trim()) {
                isValid = false;
                showError(element, 'This field is required');
            }
        });

        if (!isValid) return;

        // Get price directly from data attribute
        const price = parseFloat(priceDisplay.getAttribute('data-price'));
        if (isNaN(price) || price <= 0) {
            showToast("Invalid room price. Please select a valid room.", "error");
            return;
        }

        // Convert to smallest currency unit (pesewas)
        const amount = Math.round(price * 100);

        //Get Room 
        const roomTypeId = roomTypeSelect.value;
        const roomId = roomSelect.value;
        const selectedRoomOption = roomSelect.options[roomSelect.selectedIndex];
        const roomNumber = selectedRoomOption ? selectedRoomOption.getAttribute('data-room_number') : '';
        const notesValue = notes.value;

        // Prepare form data
        const formData = new FormData(bookingForm);
        formData.append('hostel_id', '<?= $hostel_id; ?>');
        formData.append('room_id', roomId);
        formData.append('roomNumber', roomNumber);
        formData.append('roomTypeId', roomTypeId);
        formData.append('notes', notesValue);

        try {
            // Initiate Paystack payment
            showLoading(submitButton);

            let handler = PaystackPop.setup({
                key: 'pk_test_3a243aa0a24572b40ef92531641e5809cd500d3b', // Replace with your Paystack public key
                email: formData.get('email'),
                amount: amount,
                currency: "GHS",
                ref: 'BOOK_' + Math.floor((Math.random() * 1000000000) + 1),
                onClose: function() {
                    hideLoading(submitButton);
                    showToast('Payment cancelled.', 'warning');
                },
                callback: function(response) {
                    // Payment successful; append payment reference and paid flag
                    formData.append('payment_reference', response.reference);
                    formData.append('paid', '1');
                    formData.append('amount', price);

                    processBooking(formData)
                        .then(() => {
                            console.log('Booking processed successfully.');
                        })
                        .catch(error => {
                            console.error('Error processing booking:', error);
                            Swal.fire({
                                title: 'Payment Processed',
                                text: 'Payment was successful but booking failed: ' +
                                    error.message,
                                icon: 'warning',
                                timer: 4000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        });
                }
            });

            handler.openIframe();
        } catch (error) {
            hideLoading(submitButton);
            console.error('Payment initialization error:', error);
            showToast('Failed to initialize payment: ' + error.message, 'error');
        }
    });

    // Process booking via AJAX after payment
    async function processBooking(formData) {
        try {
            showLoading(bookingForm);

            const response = await fetch('process_booking.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const result = await response.json();
            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Booking successful!',
                    icon: 'success',
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = './../booking/booking_details.php?id=' + result.booking_id;
                });
            } else {
                throw new Error(result.message || 'Booking failed');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to process booking: ' + error.message, 'error');
        } finally {
            hideLoading(bookingForm);
        }
    }
    </script>
</body>

</html>