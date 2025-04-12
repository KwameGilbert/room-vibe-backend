<?php $baseUrl = "http://" . $_SERVER["HTTP_HOST"] . "/room-vibe-backend/frontend"; ?>
<link rel="icon" type="image/x-icon" href="<?php echo $baseUrl ?>/images/room.png">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $baseUrl ?>/images/room.png">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#fd7e14',
                    dark: '#e76b00' // Optionally, you can define more shades if needed.
                },
                secondary: {
                    100: '#fff7e6', // A very light tint of secondary color
                    400: '#fcd34d', // A lighter tone (for hover effects or lighter backgrounds)
                    500: '#fbbf24', // Your base secondary color
                    600: '#f8b400' // A slightly darker tone
                },
                'baby-powder': '#FFFFFC'
            },
            fontFamily: {
                poppins: ['Poppins', 'sans-serif']
            }
        }
    }
}
</script>


<!-- Load Tailwind CSS from the CDN -->
<script src="https://cdn.tailwindcss.com"></script>