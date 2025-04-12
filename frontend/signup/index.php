<html lang="en" class="h-full w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomVibe Registration</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    'poppins': ['Poppins', 'sans-serif']
                },
                colors: {
                    'primary': '#fd7e14',
                    'primary-dark': '#e76b00'
                }
            }
        }
    }
    </script>
</head>

<body class="h-full w-full overflow-auto bg-gray-100 text-gray-800 font-poppins flex flex-col">

    <!-- Notification Container -->
    <div id="notification"
        class="fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50">
    </div>

    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40">
        <div class="max-w-md mx-auto px-5 py-3 flex items-center">
            <button id="back-button" class="text-gray-500 mr-3 hidden">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progress-bar" class="bg-primary h-2.5 rounded-full transition-all duration-300"
                    style="width: 33%"></div>
            </div>
            <div class="ml-3 text-xs font-medium text-gray-500">
                <span id="step-indicator">1/3</span>
            </div>
        </div>
    </div>

    <div class="w-full max-w-md mx-auto px-5 flex flex-col justify-center min-h-full pt-20">
        <div class="flex-1 flex flex-col justify-center">
            <!-- Section 1: Account Credentials -->
            <section id="section-1" class="mb-4">
                <div class="text-center mb-5">
                    <!-- Registration Illustration -->
                    <img src="./../images/storyset/signup_credentials.svg" alt="Sign Up Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">Create Your Account</h1>
                    <p class="text-sm text-gray-500">Set up your login credentials</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-md mb-4">
                    <form id="credentials-form">
                        <div class="mb-4">
                            <label for="email" class="block mb-1.5 text-gray-700 font-medium text-sm">Email</label>
                            <div class="relative">
                                <i
                                    class="fas fa-envelope absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password"
                                class="block mb-1.5 text-gray-700 font-medium text-sm">Password</label>
                            <div class="relative">
                                <i
                                    class="fas fa-lock absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="password" id="password" name="password" placeholder="Create a password"
                                    required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm-password" class="block mb-1.5 text-gray-700 font-medium text-sm">Confirm
                                Password</label>
                            <div class="relative">
                                <i
                                    class="fas fa-lock absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="password" id="confirm-password" name="confirm-password"
                                    placeholder="Confirm your password" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <button type="submit" id="continue-to-personal"
                            class="w-full bg-primary text-white border-0 rounded-lg py-3 text-base font-semibold 
                                  cursor-pointer transition-colors hover:bg-primary-dark mt-1 flex items-center justify-center">
                            <span>Continue</span>
                        </button>
                    </form>
                </div>

                <div class="text-center mt-0 text-gray-500 text-sm">
                    Already have an account? <a href="./../login"
                        class="text-primary font-semibold hover:underline">Sign
                        in</a>
                </div>
            </section>

            <!-- Section 2: Personal Details -->
            <section id="section-2" class="mb-4 hidden">
                <div class="text-center mb-5">
                    <!-- Personal Details Illustration -->
                    <img src="./../images/storyset/signup_personal.svg" alt="Personal Details Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">Personal Information</h1>
                    <p class="text-sm text-gray-500">Tell us a bit about yourself</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-md mb-4">
                    <form id="personal-form">
                        <div class="mb-4">
                            <label for="firstname" class="block mb-1.5 text-gray-700 font-medium text-sm">First
                                Name</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="text" id="firstname" name="firstname" placeholder="Enter your first name"
                                    required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="lastname" class="block mb-1.5 text-gray-700 font-medium text-sm">Last
                                Name</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="text" id="lastname" name="lastname" placeholder="Enter your last name"
                                    required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="gender" class="block mb-1.5 text-gray-700 font-medium text-sm">Gender</label>
                            <div class="relative">
                                <i
                                    class="fas fa-venus-mars absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <select id="gender" name="gender" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all appearance-none bg-white
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block mb-1.5 text-gray-700 font-medium text-sm">Contact
                                Number</label>
                            <div class="relative">
                                <i
                                    class="fas fa-phone absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <button type="submit" id="continue-to-school"
                            class="w-full bg-primary text-white border-0 rounded-lg py-3 text-base font-semibold 
                                  cursor-pointer transition-colors hover:bg-primary-dark mt-1 flex items-center justify-center">
                            <span>Continue</span>
                        </button>
                    </form>
                </div>
            </section>

            <!-- Section 3: School Selection -->
            <section id="section-3" class="mb-4 hidden">
                <div class="text-center mb-5">
                    <!-- School Selection Illustration -->
                    <img src="./../images/storyset/signup_school.svg" alt="School Selection Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">School Information</h1>
                    <p class="text-sm text-gray-500">Let us know your school details</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-md mb-4">
                    <form id="school-form">
                        <div class="mb-4">
                            <label for="school" class="block mb-1.5 text-gray-700 font-medium text-sm">Select
                                School</label>
                            <div class="relative">
                                <i
                                    class="fas fa-school absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <select id="school" name="school" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all appearance-none bg-white
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                                    <option value="" disabled selected>Select your school</option>
                                    <?php
                                    require_once __DIR__ . '/../config/Database.php';
                                    $database = new Database();
                                    $conn = $database->getConnection();

                                    try {
                                        // Fetch schools from database
                                        $stmt = $conn->query("SELECT id, name, logo_url FROM school ORDER BY name");
                                        $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // Output each school as an option
                                        foreach ($schools as $school) {
                                            $logo = $school['logo_url'] ?: './../images/ams_app_icon-removebg-preview (1).png';
                                            echo "<option value='" . htmlspecialchars($school['id']) . "' data-logo='" . htmlspecialchars($logo) . "'>" .
                                                 htmlspecialchars($school['name']) . "</option>\n";
                                        }
                                        
                                    } catch(PDOException $e) {
                                       
                                    }
                                    ?>
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <div id="other-school-container" class="mb-4 hidden">
                            <label for="other-school" class="block mb-1.5 text-gray-700 font-medium text-sm">Specify
                                School</label>
                            <div class="relative">
                                <i
                                    class="fas fa-building-columns absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="text" id="other-school" name="other-school"
                                    placeholder="Enter your school name"
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="student-id" class="block mb-1.5 text-gray-700 font-medium text-sm">Student ID
                                (Optional)</label>
                            <div class="relative">
                                <i
                                    class="fas fa-id-card absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <input type="text" id="student-id" name="student-id" placeholder="Enter your student ID"
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="year" class="block mb-1.5 text-gray-700 font-medium text-sm">Year of
                                Study</label>
                            <div class="relative">
                                <i
                                    class="fas fa-calendar-alt absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                                <select id="year" name="year" required
                                    class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all appearance-none bg-white
                                          focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                                    <option value="" disabled selected>Select your year</option>
                                    <option value="1">First Year</option>
                                    <option value="2">Second Year</option>
                                    <option value="3">Third Year</option>
                                    <option value="4">Fourth Year</option>
                                    <option value="5+">Fifth Year or Above</option>
                                    <option value="graduate">Graduate Student</option>
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <button type="submit" id="complete-registration"
                            class="w-full bg-primary text-white border-0 rounded-lg py-3 text-base font-semibold 
                                  cursor-pointer transition-colors hover:bg-primary-dark mt-1 flex items-center justify-center">
                            <span>Complete Registration</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <!-- Script for multi-step form -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sections
        const section1 = document.getElementById('section-1');
        const section2 = document.getElementById('section-2');
        const section3 = document.getElementById('section-3');

        // Forms
        const credentialsForm = document.getElementById('credentials-form');
        const personalForm = document.getElementById('personal-form');
        const schoolForm = document.getElementById('school-form');

        // Buttons
        const continueToPersonal = document.getElementById('continue-to-personal');
        const continueToSchool = document.getElementById('continue-to-school');
        const completeRegistration = document.getElementById('complete-registration');
        const backButton = document.getElementById('back-button');

        // Progress indicators
        const progressBar = document.getElementById('progress-bar');
        const stepIndicator = document.getElementById('step-indicator');

        // Notification container
        const notificationContainer = document.getElementById('notification');

        // Current step tracker
        let currentStep = 1;

        // Form data storage
        const formData = {};

        // Function to show notification
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = type === 'success' ?
                'bg-green-500 text-white p-4 shadow-lg' :
                'bg-red-500 text-white p-4 shadow-lg';
            notification.innerHTML = `
                <div class="max-w-md mx-auto flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="${type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'} mr-3"></i>
                        <p>${message}</p>
                    </div>
                    <button class="text-white focus:outline-none" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            // Clear previous notifications and add the new one
            notificationContainer.innerHTML = '';
            notificationContainer.appendChild(notification);

            // Slide in the notification
            setTimeout(() => {
                notificationContainer.classList.add('translate-y-0');
                notificationContainer.classList.remove('-translate-y-full');
            }, 100);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                notificationContainer.classList.remove('translate-y-0');
                notificationContainer.classList.add('-translate-y-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        // Set button loading state
        function setButtonLoading(button, isLoading) {
            const buttonText = button.querySelector('span');
            if (isLoading) {
                buttonText.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                button.disabled = true;
                button.classList.add('opacity-75');
            } else {
                buttonText.textContent = button === completeRegistration ? 'Complete Registration' : 'Continue';
                button.disabled = false;
                button.classList.remove('opacity-75');
            }
        }

        // Update progress bar
        function updateProgress(step) {
            const progressPercentage = step * 33.33;
            progressBar.style.width = `${progressPercentage}%`;
            stepIndicator.textContent = `${step}/3`;

            if (step > 1) {
                backButton.classList.remove('hidden');
            } else {
                backButton.classList.add('hidden');
            }
        }

        // Validate credentials form
        function validateCredentialsForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Please enter a valid email address.', 'error');
                return false;
            }

            // Password length check
            if (password.length < 8) {
                showNotification('Password must be at least 8 characters long.', 'error');
                return false;
            }

            // Password match check
            if (password !== confirmPassword) {
                showNotification('Passwords do not match.', 'error');
                return false;
            }

            return true;
        }

        // Validate personal form
        function validatePersonalForm() {
            const firstname = document.getElementById('firstname').value;
            const lastname = document.getElementById('lastname').value;
            const gender = document.getElementById('gender').value;
            const phone = document.getElementById('phone').value;

            if (!firstname || !lastname || !gender || !phone) {
                showNotification('Please fill in all required fields.', 'error');
                return false;
            }

            // Simple phone number validation (can be customized)
            const phoneRegex = /^\d{10,15}$/;
            if (!phoneRegex.test(phone.replace(/[^0-9]/g, ''))) {
                showNotification('Please enter a valid phone number.', 'error');
                return false;
            }

            return true;
        }

        // Validate school form
        function validateSchoolForm() {
            const school = document.getElementById('school').value;
            const otherSchool = document.getElementById('other-school').value;
            const year = document.getElementById('year').value;

            if (!school || !year) {
                showNotification('Please fill in all required fields.', 'error');
                return false;
            }

            if (school === 'other' && !otherSchool) {
                showNotification('Please specify your school name.', 'error');
                return false;
            }

            return true;
        }

        // Show "Other School" field if "Other" is selected
        document.getElementById('school').addEventListener('change', function() {
            const otherSchoolContainer = document.getElementById('other-school-container');
            if (this.value === 'other') {
                otherSchoolContainer.classList.remove('hidden');
                document.getElementById('other-school').setAttribute('required', 'required');
            } else {
                otherSchoolContainer.classList.add('hidden');
                document.getElementById('other-school').removeAttribute('required');
            }
        });

        // Navigate to next section
        function goToSection(section) {
            // Hide all sections
            section1.classList.add('hidden');
            section2.classList.add('hidden');
            section3.classList.add('hidden');

            // Show the target section
            section.classList.remove('hidden');
        }

        // Go back button handler
        backButton.addEventListener('click', function() {
            if (currentStep === 2) {
                currentStep = 1;
                goToSection(section1);
            } else if (currentStep === 3) {
                currentStep = 2;
                goToSection(section2);
            }

            updateProgress(currentStep);
        });

        // Credentials form submit handler
        credentialsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateCredentialsForm()) {
                // Store form data
                formData.email = document.getElementById('email').value;
                formData.password = document.getElementById('password').value;

                // Move to next section
                currentStep = 2;
                goToSection(section2);
                updateProgress(currentStep);
            }
        });

        // Personal form submit handler
        personalForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validatePersonalForm()) {
                // Store form data
                formData.firstname = document.getElementById('firstname').value;
                formData.lastname = document.getElementById('lastname').value;
                formData.gender = document.getElementById('gender').value;
                formData.phone = document.getElementById('phone').value;

                // Move to next section
                currentStep = 3;
                goToSection(section3);
                updateProgress(currentStep);
            }
        });

        // School form submit handler
        schoolForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateSchoolForm()) {
                // Show loading state
                setButtonLoading(completeRegistration, true);

                // Store form data
                formData.school = document.getElementById('school').value;
                if (formData.school === 'other') {
                    formData.schoolName = document.getElementById('other-school').value;
                }
                formData.studentId = document.getElementById('student-id').value;
                formData.year = document.getElementById('year').value;



                fetch('register_process.php', {
                        method: 'POST',
                        body: JSON.stringify(formData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        setButtonLoading(completeRegistration, false);
                        if (data.success) {
                            showNotification('Registration successful!', 'success');
                            setTimeout(() => {
                                window.location.href = './../login/';
                            }, 2000);
                        } else {
                            showNotification(data.message ||
                                'Registration failed. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        setButtonLoading(completeRegistration, false);
                        showNotification('Connection error. Please check your internet connection.',
                            'error');
                        console.error('Error:', error);
                    });
            }
        });

        // Responsive adjustments for smaller screens
        function adjustForScreenHeight() {
            const vh = window.innerHeight;
            if (vh < 700) {
                document.querySelectorAll('h1').forEach(el => {
                    el.classList.remove('text-2xl');
                    el.classList.add('text-xl');
                });
            }
            if (vh < 600) {
                // Additional adjustments for very small screens
            }
        }
        window.addEventListener('load', adjustForScreenHeight);
        window.addEventListener('resize', adjustForScreenHeight);
    });
    </script>
</body>

</html>