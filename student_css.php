<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Custom Admin CSS -->
<link rel="stylesheet" type="text/css" href="admin.css?v=9">
<!-- Bootstrap 5 JS bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (for DataTables later) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Session Auto-Logout Script -->
<script>
    let idleTime = 0;
    $(document).ready(function () {
        // Increment the idle time counter every second
        setInterval(function() {
            idleTime++;
            if (idleTime >= 90) { // 2 minutes (120 seconds)
                window.location.href = 'logout.php?reason=timeout';
            }
        }, 1000); 

        // Reset timer on any activity
        $(this).on('mousemove keypress click scroll', function () {
            idleTime = 0;
        });
    });
</script>
<script>
$(document).ready(function() {
    let currentPage = window.location.pathname.split('/').pop();
    if(currentPage !== 'studenthome.php' && currentPage !== 'index.php' && currentPage !== '') {
        $('.content').prepend('<a href="studenthome.php" class="btn btn-outline-info mb-4"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>');
    }
});
</script>


<script>
  console.log('%c Aether Academy %c Developed by Uday Praveen (SICSR Pune) ', 'background: #06b6d4; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #8b5cf6; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
</script>

