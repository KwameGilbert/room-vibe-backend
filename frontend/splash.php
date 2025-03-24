<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RoomVibe Splash</title>
    <!-- Tailwind CSS CDN for demonstration purposes -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Set background image and cover entire viewport */
    body {
        background: url('./images/hero-bg.cd29a761.jpg') no-repeat center center/cover;
    }
    </style>
</head>

<body class="relative h-screen overflow-hidden flex items-center justify-center">
    <!-- Black overlay with transparency -->
    <div class="absolute inset-0 bg-black opacity-50"></div>

    <!-- Splash content container -->
    <div class="relative z-10 w-full h-screen max-w-md px-6 flex flex-col justify-around py-5">
        <!-- Top content section -->
        <div class="text-center">
            <!-- Storyset SVG image -->
            <div>
                <img src="./images/storyset/room-searching.svg" id="storyset" alt="Room Search Illustration"
                    class="w-64 h-64 mx-auto">
            </div>
            <!-- App title -->
            <div class="mt-2">
                <h1 class="text-4xl font-bold text-white tracking-wider drop-shadow-lg">
                    Room<span class="text-[#fd7e14]">Vibe</span>
                </h1>
            </div>
            <!-- Slogan and Tagline -->
            <p class="text-md text-white whitespace-pre-line mt-2">
                Hostel bookings made easy
                Discover your perfect room on campus
            </p>
        </div>

        <!-- Call-to-Action Buttons at bottom -->
        <div class="w-full text-center">
            <div class="flex flex-col gap-4">
                <a href="./signup/" class="bg-[#fd7e14] hover:bg-orange-600 text-white py-3 rounded-lg font-semibold">
                    Sign Up Now
                </a>
                <a href="./login/" class="bg-white hover:bg-gray-200 text-[#fd7e14] py-3 rounded-lg font-semibold">
                    Login
                </a>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Example: add a class to the storyset image after 1 second if needed.
        setTimeout(() => {
            document.querySelector('#storyset').classList.add('animated');
        }, 1000);
    });
    </script>
</body>

</html>