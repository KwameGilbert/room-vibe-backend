<html lang="en" class="h-full w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomVibe Login</title>
    <?php include_once './../includes/links.php';?>
</head>

<body class="h-full w-full overflow-auto bg-gray-100 text-gray-800 font-poppins flex flex-col">

    <!-- Notification Container -->
    <div id="notification"
        class="fixed top-0 left-0 right-0 transform -translate-y-full transition-transform duration-300 ease-in-out z-50">
    </div>

    <div class="w-full max-w-md mx-auto px-5 flex flex-col justify-center min-h-full">
        <div class="flex-1 flex flex-col justify-center">
            <div class="text-center mb-5">
                <!-- Login Illustration -->
                <img src="./../images/storyset/login_animate.svg" alt="Login Illustration"
                    class="w-full max-w-[180px] h-auto mx-auto mb-4 block">
                <h1 class="text-2xl font-bold text-primary mb-1">Welcome to RoomVibe</h1>
                <p class="text-sm text-gray-500">Sign in to continue</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-md mb-4">
                <!-- Removed inline error message div from form -->
                <form id="login-form" method="POST">
                    <div class="mb-4">
                        <label for="email" class="block mb-1.5 text-gray-700 font-medium text-sm">Email</label>
                        <div class="relative">
                            <i
                                class="fas fa-user absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required
                                class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                       focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block mb-1.5 text-gray-700 font-medium text-sm">Password</label>
                        <div class="relative">
                            <i
                                class="fas fa-lock absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 text-base"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password"
                                required
                                class="w-full py-3 pl-10 pr-3 border border-gray-300 rounded-lg text-sm transition-all
                                       focus:outline-none focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                        </div>
                        <div class="text-right mt-1">
                            <a href="#" class="text-xs text-gray-500 hover:text-primary transition-colors">Forgot
                                password?</a>
                        </div>
                    </div>

                    <button type="submit" id="login-button"
                        class="w-full bg-primary text-white border-0 rounded-lg py-3 text-base font-semibold 
                               cursor-pointer transition-colors hover:bg-primary-dark mt-1 flex items-center justify-center">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <div class="relative text-xs text-gray-500 mb-3">
                        <span class="relative z-10 px-2 bg-white">Or sign in with</span>
                        <div class="absolute left-0 top-1/2 w-full h-px bg-gray-200 -z-0"></div>
                    </div>
                    <div class="flex justify-center gap-4">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 
                                    text-gray-500 text-lg cursor-pointer transition-all hover:bg-gray-50 hover:-translate-y-0.5">
                            <i class="fab fa-google"></i>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 
                                    text-gray-500 text-lg cursor-pointer transition-all hover:bg-gray-50 hover:-translate-y-0.5">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 
                                    text-gray-500 text-lg cursor-pointer transition-all hover:bg-gray-50 hover:-translate-y-0.5">
                            <i class="fab fa-apple"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-0 text-gray-500 text-sm">
                Don't have an account? <a href="./../signup/"
                    class="text-primary font-semibold hover:underline">Register now</a>
            </div>
        </div>
    </div>

    <!-- Responsive adjustments for smaller screens -->
    <script>
    // This script adjusts elements for very small screens
    function adjustForScreenHeight() {
        const vh = window.innerHeight;
        if (vh < 700) {
            document.querySelector('h1').classList.remove('text-2xl');
            document.querySelector('h1').classList.add('text-xl');
        }
        if (vh < 600) {
            document.querySelectorAll('.w-10').forEach(el => {
                el.classList.remove('w-10', 'h-10');
                el.classList.add('w-9', 'h-9');
            });
        }
        if (vh < 500) {
            document.querySelector('.justify-center').classList.remove('justify-center');
            document.querySelector('.justify-center').classList.add('justify-start');
        }
    }
    window.addEventListener('load', adjustForScreenHeight);
    window.addEventListener('resize', adjustForScreenHeight);
    </script>

    <!-- Login Process Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const loginButton = document.getElementById('login-button');
        const notificationContainer = document.getElementById('notification');

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
        function setButtonLoading(isLoading) {
            const buttonText = loginButton.querySelector('span');
            if (isLoading) {
                buttonText.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Signing in...
                `;
                loginButton.disabled = true;
                loginButton.classList.add('opacity-75');
            } else {
                buttonText.textContent = 'Sign In';
                loginButton.disabled = false;
                loginButton.classList.remove('opacity-75');
            }
        }

        // Form submission event
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Hide any inline error (if it exists) - not used now
            // Show loading state
            setButtonLoading(true);
            // Prepare form data
            const formData = new FormData(loginForm);
            // Send AJAX request
            fetch('login_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setButtonLoading(false);
                    if (data.success) {
                        // Show success notification
                        showNotification('Logged in successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = './../';
                        }, 1500);
                    } else {
                        // Show error notification instead of inline error
                        showNotification(data.message || 'An error occurred. Please try again.',
                            'error');
                    }
                })
                .catch(error => {
                    setButtonLoading(false);
                    showNotification('Connection error. Please check your internet connection.',
                        'error');
                    console.error('Error:', error);
                });
        });
    });
    </script>
</body>

</html>