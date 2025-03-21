<?php
session_start();
if (!isset($_SESSION['student_id'])) {
header("Location: ./login/");
exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Vibe</title>
    <?php include './components/links.php'; ?>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-gray-100">
    <!-- Page Content -->
    <div id="page-content"></div>

    <?php include './components/navbar.php' ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const defaultPage = "explore";
        const activeColor = "#fd7e14";
        const inactiveColor = "gray";
        const pageContent = document.getElementById("page-content");
        const navLinks = document.querySelectorAll(".nav-link");

        // Function to update active nav and load page
        function updateActiveNav(page) {
            // Remove active color from all
            navLinks.forEach(link => {
                link.querySelector("i").style.color = inactiveColor;
                link.querySelector("p").style.color = inactiveColor;
            });



            // Load the requested page
            fetch(`<?php echo $baseUrl ?>/${page}`)
                .then(response => response.text())
                .then(data => {
                    // Set active color
                    const activeItem = document.querySelector(`[data-page="${page}"]`);
                    if (activeItem) {
                        activeItem.querySelector("i").style.color = activeColor;
                        activeItem.querySelector("p").style.color = activeColor;
                    }

                    pageContent.innerHTML = data;
                })
                .catch(error => console.error("Error loading page:", error));
        }

        // Load default page initially
        updateActiveNav(defaultPage);

        // Event listener for navigation clicks
        navLinks.forEach(link => {
            link.addEventListener("click", function() {
                const page = this.getAttribute("data-page");
                updateActiveNav(page);
            });
        });
    });
    </script>
</body>

</html>