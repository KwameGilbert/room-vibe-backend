<link rel="icon" type="image/x-icon" href="<?php echo $baseUrl ?>/images/room.png">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $baseUrl ?>/images/room.png">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php $baseUrl = "http://" . $_SERVER["HTTP_HOST"] . "/room-vibe-backend/frontend"; ?>


<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Tailwind Configurations -->
<script>
tailwind.config = {
    theme: {
        extend: {
            fontFamily: {
                'poppins': ['Poppins', 'sans-serif']
            },
            colors: {
                'primary': '#fd7e14',
                'primary-dark': '#e76b00',
                'secondary': '#fbbf24',
                'baby-powder': '#FFFFFC'
            }
        }
    }
}
</script>