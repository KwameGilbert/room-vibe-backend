        </div> <!-- End of main content container -->
        </div> <!-- End of flex container -->

        <!-- SweetAlert2 for notifications -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (isset($successMessage)): ?>
        <script>
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '<?= $successMessage ?>',
    timer: 4000,
    toast: true,
    position: 'top-end',
    showConfirmButton: false
});
        </script>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
        <script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= $errorMessage ?>',
    timer: 4000,
    toast: true,
    position: 'top-end',
    showConfirmButton: false
});
        </script>
        <?php endif; ?>

        <!-- Any additional scripts can go here -->

        </body>

        </html>