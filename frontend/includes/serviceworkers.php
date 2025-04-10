 <script>
// if Service Workers are supported
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('./scripts/service-worker.min.js').then((registration) => {
            console.log('Service Worker registered successfully with scope: ', registration.scope);
        }).catch((error) => {
            console.log('Service Worker registration failed: ', error);
        });
    });
};
 </script>