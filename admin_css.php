<!-- Theme Initialization (FOUC Prevention) -->
<script>
    (function() {
        var theme = localStorage.getItem('aether-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<!-- Custom Admin CSS -->
<link rel="stylesheet" type="text/css" href="admin.css?v=10">

<!-- Bootstrap 5 JS bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (for DataTables later) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Theme Handler & Global Scripts -->
<script>
    function toggleTheme() {
        var current = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', current);
        localStorage.setItem('aether-theme', current);
        updateThemeIcons(current);
    }

    function updateThemeIcons(theme) {
        $('.theme-toggle-btn i').each(function() {
            if(theme === 'light') {
                $(this).removeClass('fa-moon').addClass('fa-sun');
            } else {
                $(this).removeClass('fa-sun').addClass('fa-moon');
            }
        });
    }

    $(document).ready(function() {
        var theme = localStorage.getItem('aether-theme') || 'dark';
        updateThemeIcons(theme);
        $(document).on('click', '.theme-toggle-btn', function(e) {
            e.preventDefault();
            toggleTheme();
        });
    });
</script>

<!-- Session Auto-Logout Script -->
<script>
    let idleTime = 0;
    $(document).ready(function () {
        setInterval(function() {
            idleTime++;
            if (idleTime >= 90) { 
                window.location.href = 'logout.php?reason=timeout';
            }
        }, 1000); 

        $(this).on('mousemove keypress click scroll', function () {
            idleTime = 0;
        });
    });
</script>

<!-- Dynamic Back to Dashboard button -->
<script>
$(document).ready(function() {
    let currentPage = window.location.pathname.split('/').pop();
    if(currentPage !== 'adminhome.php' && currentPage !== 'index.php' && currentPage !== '') {
        $('.content').prepend('<a href="adminhome.php" class="btn btn-outline-info mb-4"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>');
    }
});
</script>

<script>
  console.log('%c Aether Academy %c Developed by Uday Praveen (SICSR Pune) ', 'background: #06b6d4; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #8b5cf6; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
</script>
