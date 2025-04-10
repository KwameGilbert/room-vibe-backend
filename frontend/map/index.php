    <div id="mapheader">
        <h1 class="bg-orange-400 text-white text-xl font-semibold text-center py-3 mb-4">Maps</h1>
    </div>
    <div class="text-center flex flex-col items-center justify-center h-max mt-20">
        <img id="storyset" src="./images/storyset/gps-navigator-animate.svg" class="h-full w-full">
        <p class='text-center text-gray-500'>
            Your map will be ready soon...
        </p>
    </div>

    <script type='text/javascript'>
window.onload = function() {
    setTimeout(function() {
        document.querySelector('#storyset').classList.add('animated');
    }, 2000);
}
    </script>