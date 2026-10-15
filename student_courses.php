<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'admin') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);
$username = $_SESSION['username'];

// Get user ID
$u_res = mysqli_query($data, "SELECT id FROM user WHERE username='$username'");
$u_info = mysqli_fetch_assoc($u_res);
$student_id = $u_info['id'];

// Get enrolled courses with instructor name
$sql = "SELECT course.id as course_id, course.name as course_name, teacher.name as teacher_name 
        FROM enrollment 
        JOIN course ON enrollment.course_id = course.id 
        JOIN teacher ON course.teacher_id = teacher.id 
        WHERE enrollment.student_id = '$student_id'";
$result = mysqli_query($data, $sql);

// Deterministic gradients for course banners
$gradients = [
    'linear-gradient(135deg, #059669, #10b981)', // Green
    'linear-gradient(135deg, #7c3aed, #8b5cf6)', // Purple
    'linear-gradient(135deg, #d97706, #f59e0b)', // Amber
    'linear-gradient(135deg, #db2777, #ec4899)', // Pink/Magenta
    'linear-gradient(135deg, #0284c7, #38bdf8)', // Blue
    'linear-gradient(135deg, #ea580c, #f97316)'  // Orange
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses | Aether Academy</title>
    <?php include 'student_css.php'; ?>
    <style>
        /* Toolbar Controls Container */
        .courses-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 28px;
        }

        /* Search Input Group */
        .search-group {
            position: relative;
            min-width: 260px;
            max-width: 360px;
        }
        .search-group .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-orange);
            font-size: 0.95rem;
            pointer-events: none;
            z-index: 5;
        }
        .search-group input {
            height: 42px;
            padding-left: 40px !important;
            padding-right: 14px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        /* Universal Custom Dropdown Styling (Fixes overlapping and missing arrows) */
        .course-select {
            height: 42px !important;
            padding: 8px 38px 8px 14px !important;
            border-radius: 8px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 12px 8px !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Dark Mode Select (Vibrant Orange Arrow & High Contrast) */
        :root .course-select,
        [data-theme="dark"] .course-select {
            background-color: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 10'%3e%3cpath fill='none' stroke='%23f97316' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.4' d='m2 2 6 6 6-6'/%3e%3c/svg%3e") !important;
        }
        :root .course-select:focus,
        [data-theme="dark"] .course-select:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.25) !important;
        }

        /* Light Mode Select (Proper Padding & Orange Arrow) */
        [data-theme="light"] .course-select {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 10'%3e%3cpath fill='none' stroke='%23ea580c' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.4' d='m2 2 6 6 6-6'/%3e%3c/svg%3e") !important;
        }
        [data-theme="light"] .course-select:focus {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 0.25rem rgba(234, 88, 12, 0.2) !important;
        }

        /* Distinct min-widths to prevent any text clipping */
        .select-filter { min-width: 110px; }
        .select-sort { min-width: 210px; }
        .select-view { min-width: 120px; }

        /* Course Cards Layout */
        .course-card {
            border-radius: 12px;
            overflow: hidden;
            background-color: #111827;
            border: 1px solid #1f2937;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        [data-theme="light"] .course-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
        }
        .course-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-orange);
            box-shadow: 0 10px 25px rgba(0,0,0,0.18);
        }
        .course-banner {
            height: 140px;
            width: 100%;
            position: relative;
        }
        .course-banner::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 90%, rgba(0,0,0,0.15) 0%, transparent 50%);
        }
        .course-body {
            padding: 18px 20px 22px;
            position: relative;
            flex-grow: 1;
        }
        .course-title {
            color: #38bdf8 !important;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 6px;
        }
        [data-theme="light"] .course-title {
            color: #0284c7 !important;
        }
        .course-meta {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        [data-theme="light"] .course-meta {
            color: #64748b;
        }
        .course-dots {
            position: absolute;
            right: 18px;
            bottom: 18px;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .course-dots:hover {
            color: var(--accent-orange);
        }

        /* List View Mode */
        .list-view-item {
            flex-direction: row !important;
            align-items: center;
        }
        .list-view-item .course-banner {
            width: 180px;
            height: 100px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="fw-bold mb-0" style="color: var(--accent-orange);">
            <i class="fas fa-book-reader me-2"></i> Course Overview
        </h2>
    </div>

    <!-- Toolbar Controls (Active Search, Sized Dropdowns & High-Contrast Arrows) -->
    <div class="courses-toolbar">
        <!-- Category Filter -->
        <select id="categoryFilter" class="form-select course-select select-filter">
            <option value="all">All</option>
            <option value="law">Law</option>
            <option value="design">Design</option>
            <option value="ethics">Ethics</option>
        </select>

        <!-- Live Search Input -->
        <div class="search-group flex-grow-1">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="courseSearch" class="form-control" placeholder="Search courses or instructor...">
        </div>

        <!-- Sort Select -->
        <select id="sortFilter" class="form-select course-select select-sort ms-auto">
            <option value="name-asc">Sort by course name (A-Z)</option>
            <option value="name-desc">Sort by course name (Z-A)</option>
        </select>

        <!-- View Mode Switcher -->
        <select id="viewModeFilter" class="form-select course-select select-view">
            <option value="card">Card</option>
            <option value="list">List</option>
        </select>
    </div>

    <!-- Courses Grid -->
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div id="coursesGrid" class="row g-4">
            <?php 
            $i = 0;
            while($row = mysqli_fetch_assoc($result)): 
                $grad_idx = $row['course_id'] % count($gradients);
                $bg_gradient = $gradients[$grad_idx];
                $i++;
            ?>
            <div class="col-md-4 col-sm-6 course-col" 
                 data-title="<?php echo htmlspecialchars($row['course_name']); ?>" 
                 data-instructor="<?php echo htmlspecialchars($row['teacher_name']); ?>">
                <div class="course-card">
                    <div class="course-banner" style="background: <?php echo $bg_gradient; ?>;"></div>
                    <div class="course-body">
                        <div class="course-title"><?php echo htmlspecialchars($row['course_name']); ?></div>
                        <div class="course-meta">Instructor : <?php echo htmlspecialchars($row['teacher_name']); ?></div>
                        <i class="fas fa-ellipsis-v course-dots" title="Options"></i>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info bg-transparent border-info text-info py-4 text-center rounded-3">
            <i class="fas fa-info-circle me-2"></i> You are not currently enrolled in any courses.
        </div>
    <?php endif; ?>

    <!-- No Match Alert -->
    <div id="noMatchAlert" class="alert alert-warning text-center d-none mt-4 py-4 rounded-3">
        <i class="fas fa-search me-2"></i> No courses found matching your search.
    </div>

</div>

<!-- Real-Time Filter & Sort Scripts -->
<script>
$(document).ready(function() {

    // 1. Live Instant Search & Category Filter
    function filterCourses() {
        var query = $('#courseSearch').val().toLowerCase().trim();
        var category = $('#categoryFilter').val().toLowerCase();
        var visibleCount = 0;

        $('.course-col').each(function() {
            var title = ($(this).data('title') || '').toLowerCase();
            var instructor = ($(this).data('instructor') || '').toLowerCase();

            var matchesQuery = query === '' || title.indexOf(query) !== -1 || instructor.indexOf(query) !== -1;
            var matchesCategory = category === 'all' || title.indexOf(category) !== -1;

            if(matchesQuery && matchesCategory) {
                $(this).stop(true, true).fadeIn(150);
                visibleCount++;
            } else {
                $(this).stop(true, true).hide();
            }
        });

        if(visibleCount === 0) {
            $('#noMatchAlert').removeClass('d-none');
        } else {
            $('#noMatchAlert').addClass('d-none');
        }
    }

    // Attach to input & keyup for instant responsiveness
    $('#courseSearch').on('input keyup', filterCourses);
    $('#categoryFilter').on('change', filterCourses);

    // 2. Sort Logic (A-Z, Z-A)
    $('#sortFilter').on('change', function() {
        var sortMode = $(this).val();
        var $grid = $('#coursesGrid');
        var $items = $grid.children('.course-col').get();

        $items.sort(function(a, b) {
            var nameA = ($(a).data('title') || '').toUpperCase();
            var nameB = ($(b).data('title') || '').toUpperCase();

            if (sortMode === 'name-asc') {
                return nameA.localeCompare(nameB);
            } else if (sortMode === 'name-desc') {
                return nameB.localeCompare(nameA);
            }
            return 0;
        });

        $.each($items, function(idx, item) {
            $grid.append(item);
        });
    });

    // 3. View Mode Switcher (Card / List)
    $('#viewModeFilter').on('change', function() {
        var mode = $(this).val();
        if(mode === 'list') {
            $('.course-col').removeClass('col-md-4 col-sm-6').addClass('col-12');
            $('.course-card').addClass('list-view-item');
        } else {
            $('.course-col').removeClass('col-12').addClass('col-md-4 col-sm-6');
            $('.course-card').removeClass('list-view-item');
        }
    });

});
</script>

</body>
</html>
