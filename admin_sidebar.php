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
        <a class="dashhead" href="adminhome.php">Admin Dashboard</a>
    </div>

    <div class="logout">
        <a class="btn btn-primary" href="logout.php">Logout</a>
    </div>
</header>

<aside id="sidebar">
    <ul>
        <li><a href="adminhome.php"><i class="fas fa-chart-line me-2"></i> Dashboard Overview</a></li>
        <li><a href="add_student.php"><i class="fas fa-user-plus me-2"></i> Add Student</a></li>
        <li><a href="admin_add_teacher.php"><i class="fas fa-chalkboard-teacher me-2"></i> Add Teacher</a></li>
        <li><a href="admin_add_course.php"><i class="fas fa-book-open me-2"></i> Add Course</a></li>
        <li><a href="admin_attendance.php"><i class="fas fa-clipboard-check me-2"></i> Attendance</a></li>
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

