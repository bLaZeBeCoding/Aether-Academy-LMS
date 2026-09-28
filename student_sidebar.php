<script>
if(localStorage.getItem('sidebarState') === 'collapsed') { document.body.classList.add('sidebar-collapsed'); }
</script>
<header class="header">
    <div class="d-flex align-items-center">
        <button id="sidebarToggle" class="btn btn-outline-light me-3 border-0">
            <i class="fas fa-bars fs-4"></i>
        </button>
        <a href="index.php">
            <img src="logo.jpg" alt="Logo" width="40" height="40" class="me-3 rounded-circle" style="border: 2px solid #06b6d4;">
        </a>
        <a class="dashhead" href="studenthome.php">Student Dashboard</a>
    </div>

    <div class="logout">
        <a class="btn btn-primary" href="logout.php">Logout</a>
    </div>
</header>

<aside id="sidebar">
    <ul>
        <li><a href="studenthome.php"><i class="fas fa-th-large me-2"></i> My Dashboard</a></li>
        <li><a href="student_profile.php"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
    </ul>
</aside>

<script>
    $(document).ready(function(){
        $("#sidebarToggle").click(function(){
            $("body").toggleClass("sidebar-collapsed");
            if($("body").hasClass("sidebar-collapsed")) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
            }
        });
    });
</script>

