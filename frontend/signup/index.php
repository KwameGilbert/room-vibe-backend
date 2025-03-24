<!DOCTYPE html>
<html lang="en" class="h-full w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomVibe Sign Up</title>
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
    <style>
    /* Custom style for notification container if needed */
    #notification {
        /* Initially hidden above the viewport */
        transform: translateY(-100%);
    }
    </style>
</head>

<body class="h-full w-full overflow-auto bg-gray-100 text-gray-800 font-poppins flex flex-col">
    <!-- Notification Container -->
    <div id="notification"
        class="fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50">
    </div>

    <div class="w-full max-w-md mx-auto px-5 flex flex-col justify-center min-h-full">
        <!-- Multi-Step Form Container -->
        <form id="signup-form" method="POST">
            <!-- Step 1: Credentials -->
            <div id="step-1" class="bg-white rounded-2xl p-5 shadow-md mb-4">
                <div class="text-center mb-5">
                    <!-- Storyset SVG for step 1 (replace with your own) -->
                    <img src="./../images/storyset/signup_credentials.svg" alt="Credentials Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">Sign Up - Credentials</h1>
                    <p class="text-sm text-gray-500">Create your account</p>
                </div>
                <div class="mb-4">
                    <label for="email" class="block mb-1.5 text-gray-700 font-medium text-sm">Email</label>
                    <div class="relative">
                        <i
                            class="fas fa-envelope absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-1.5 text-gray-700 font-medium text-sm">Password</label>
                    <div class="relative">
                        <i
                            class="fas fa-lock absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block mb-1.5 text-gray-700 font-medium text-sm">Confirm
                        Password</label>
                    <div class="relative">
                        <i
                            class="fas fa-lock absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm your password" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <button type="button"
                    class="w-full bg-primary text-white border-0 rounded-lg py-3 text-base font-semibold cursor-pointer transition-colors hover:bg-primary-dark mt-1 flex items-center justify-center"
                    onclick="nextStep(1)">
                    <span>Continue</span>
                </button>
                <p class="text-center mt-4 text-sm text-gray-600">
                    Already have an account?
                    <a href="../login" class="text-primary hover:text-primary-dark font-semibold">Login here</a>
                </p>
            </div>


            <!-- Step 2: Personal Details -->
            <div id="step-2" class="bg-white rounded-2xl p-5 shadow-md mb-4 hidden">
                <div class="text-center mb-5">
                    <!-- Storyset SVG for step 2 (replace with your own) -->
                    <img src="./../images/storyset/signup_personal.svg" alt="Personal Details Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">Personal Details</h1>
                    <p class="text-sm text-gray-500">Tell us about yourself</p>
                </div>
                <div class="mb-4">
                    <label for="first_name" class="block mb-1.5 text-gray-700 font-medium text-sm">First Name</label>
                    <div class="relative">
                        <i
                            class="fas fa-user absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name"
                            required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="last_name" class="block mb-1.5 text-gray-700 font-medium text-sm">Last Name</label>
                    <div class="relative">
                        <i
                            class="fas fa-user absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="gender" class="block mb-1.5 text-gray-700 font-medium text-sm">Gender</label>
                    <div class="relative">
                        <i
                            class="fas fa-venus-mars absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <select id="gender" name="gender" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            <option value="" disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="contact" class="block mb-1.5 text-gray-700 font-medium text-sm">Contact</label>
                    <div class="relative">
                        <i
                            class="fas fa-phone absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <input type="tel" id="contact" name="contact" placeholder="Enter your contact number" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="text-primary font-semibold" onclick="prevStep(2)">Back</button>
                    <button type="button"
                        class="bg-primary text-white border-0 rounded-lg py-3 px-6 text-base font-semibold cursor-pointer transition-colors hover:bg-primary-dark"
                        onclick="nextStep(2)">
                        Continue
                    </button>
                </div>
            </div>

            <!-- Step 3: School Selection -->
            <div id="step-3" class="bg-white rounded-2xl p-5 shadow-md mb-4 hidden">
                <div class="text-center mb-5">
                    <!-- Storyset SVG for step 3 (replace with your own) -->
                    <img src="./../images/storyset/signup_school.svg" alt="School Selection Illustration"
                        class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                    <h1 class="text-2xl font-bold text-primary mb-1">School Selection</h1>
                    <p class="text-sm text-gray-500">Choose your school</p>
                </div>
                <div class="mb-4">
                    <label for="school" class="block mb-1.5 text-gray-700 font-medium text-sm">School</label>
                    <div class="relative">
                        <i
                            class="fas fa-school absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                        <select id="school" name="school" required
                            class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                            <option value="" disabled selected>Select your school</option>
                            <!-- These options should be dynamically populated if available -->
                            <option value="1">School of Engineering</option>
                            <option value="2">School of Business</option>
                            <option value="3">School of Arts</option>
                            <option value="4">School of Sciences</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="text-primary font-semibold" onclick="prevStep(3)">Back</button>
                    <button type="submit" id="complete-button"
                        class="bg-primary text-white border-0 rounded-lg py-3 px-6 text-base font-semibold cursor-pointer transition-colors hover:bg-primary-dark">
                        Complete
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Scripts for multi-step navigation -->
    <script>
    // Show next step and hide current step
    function nextStep(currentStep) {
        const currentDiv = document.getElementById('step-' + currentStep);
        const nextDiv = document.getElementById('step-' + (currentStep + 1));
        if (currentDiv && nextDiv) {
            currentDiv.classList.add('hidden');
            nextDiv.classList.remove('hidden');
        }
    }
    // Show previous step and hide current step
    function prevStep(currentStep) {
        const currentDiv = document.getElementById('step-' + currentStep);
        const prevDiv = document.getElementById('step-' + (currentStep - 1));
        if (currentDiv && prevDiv) {
            currentDiv.classList.add('hidden');
            prevDiv.classList.remove('hidden');
        }
    }

    // Signup form submission
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('signup-form');
        const completeButton = document.getElementById('complete-button');
        const notificationContainer = document.getElementById('notification');

        function showNotification(message, type) {
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
            notificationContainer.innerHTML = '';
            notificationContainer.appendChild(notification);
            setTimeout(() => {
                notificationContainer.classList.add('translate-y-0');
                notificationContainer.classList.remove('-translate-y-full');
            }, 100);
            setTimeout(() => {
                notificationContainer.classList.remove('translate-y-0');
                notificationContainer.classList.add('-translate-y-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Gather form data
            const formData = new FormData(signupForm);
            // Send AJAX request to signup_process.php (implement server-side handling)
            fetch('signup_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Sign up successful!', 'success');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Sign up failed. Please try again.',
                            'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Connection error. Please check your internet connection.',
                        'error');
                });
        });
    });
    </script>
</body>

</html>