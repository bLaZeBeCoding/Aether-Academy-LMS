<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : '';
$selected_date = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : date('Y-m-d');

// Save attendance
$success_message = "";
if(isset($_POST['save_attendance'])) {
    $c_id = (int)$_POST['course_id'];
    $a_date = mysqli_real_escape_string($data, $_POST['attendance_date']);
    
    if(isset($_POST['status']) && is_array($_POST['status'])) {
        $saved_count = 0;
        foreach($_POST['status'] as $student_id => $status) {
            $s_id = (int)$student_id;
            $stat = mysqli_real_escape_string($data, $status);
            
            $check = mysqli_query($data, "SELECT id FROM attendance WHERE student_id='$s_id' AND course_id='$c_id' AND attendance_date='$a_date'");
            if(mysqli_num_rows($check) > 0) {
                mysqli_query($data, "UPDATE attendance SET status='$stat' WHERE student_id='$s_id' AND course_id='$c_id' AND attendance_date='$a_date'");
            } else {
                mysqli_query($data, "INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES ('$s_id', '$c_id', '$a_date', '$stat')");
            }
            $saved_count++;
        }
        $formatted_saved_date = date('l, M d, Y', strtotime($a_date));
        $success_message = "Successfully recorded roll call for $saved_count student" . ($saved_count > 1 ? "s" : "") . " on $formatted_saved_date!";
    }
}

$today_date = date('Y-m-d');

// All courses with teacher info & stats
$courses_query = "SELECT 
    c.id, c.name as course_name, t.name as teacher_name,
    COUNT(DISTINCT e.student_id) as enrolled_count,
    COUNT(a.id) as total_attendance_logs,
    ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.id), 0)) * 100, 1) as course_rate,
    (SELECT COUNT(*) FROM attendance WHERE course_id = c.id AND attendance_date = '$today_date') as today_marked
FROM course c
LEFT JOIN teacher t ON c.teacher_id = t.id
LEFT JOIN enrollment e ON c.id = e.course_id
LEFT JOIN attendance a ON c.id = a.course_id
GROUP BY c.id
ORDER BY c.id ASC";
$courses_res = mysqli_query($data, $courses_query);
$courses_list = [];
$selected_course_info = null;
while($row = mysqli_fetch_assoc($courses_res)) {
    $courses_list[] = $row;
    if($selected_course == $row['id']) {
        $selected_course_info = $row;
    }
}

// If course is selected, fetch students and individual attendance stats
$enrolled_students = [];
if($selected_course != '') {
    $query = "SELECT 
        user.id, 
        user.username, 
        user.email,
        (SELECT status FROM attendance WHERE student_id = user.id AND course_id = '$selected_course' AND attendance_date = '$selected_date' LIMIT 1) as current_status,
        (SELECT COUNT(*) FROM attendance WHERE student_id = user.id AND course_id = '$selected_course') as student_total_classes,
        (SELECT SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) FROM attendance WHERE student_id = user.id AND course_id = '$selected_course') as student_present_classes
    FROM enrollment 
    JOIN user ON enrollment.student_id = user.id 
    WHERE enrollment.course_id = '$selected_course'
    ORDER BY user.username ASC";
    $enrolled_res = mysqli_query($data, $query);
    while($s = mysqli_fetch_assoc($enrolled_res)) {
        $enrolled_students[] = $s;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Attendance Management | Aether Academy</title>
    <?php include 'admin_css.php'; ?>
    <style>
        .course-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .course-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-orange) !important;
            box-shadow: 0 12px 30px rgba(249, 115, 22, 0.18);
        }
        .student-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25);
            flex-shrink: 0;
            letter-spacing: 0.5px;
        }
        .btn-check:checked + .btn-outline-success {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
            box-shadow: 0 0 14px rgba(16, 185, 129, 0.45);
        }
        .btn-check:checked + .btn-outline-danger {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
            box-shadow: 0 0 14px rgba(239, 68, 68, 0.45);
        }
        .btn-outline-success {
            border-color: #10b981;
            color: #10b981;
        }
        .btn-outline-danger {
            border-color: #ef4444;
            color: #ef4444;
        }
        .btn-outline-success:hover {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }
        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        .student-row {
            transition: background-color 0.2s ease;
        }
        .student-row.row-present {
            background-color: rgba(16, 185, 129, 0.05) !important;
        }
        .student-row.row-absent {
            background-color: rgba(239, 68, 68, 0.05) !important;
        }
        .session-banner {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.12), rgba(15, 23, 42, 0.3));
            border: 1px solid rgba(249, 115, 22, 0.35);
            border-radius: 16px;
            padding: 24px;
        }
        .sticky-save-bar {
            position: sticky;
            bottom: 24px;
            z-index: 1000;
            background: var(--card-bg);
            border: 1px solid var(--accent-orange);
            border-radius: 16px;
            padding: 16px 24px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-inline-flex align-items-center gap-2 mb-2">
                <span class="badge py-2 px-3 rounded-pill" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid rgba(249,115,22,0.3); font-size: 0.82rem; font-weight: 600;">
                    <i class="fas fa-clipboard-check me-1"></i> Roll Call Command Center
                </span>
            </div>
            <h1 class="fw-bold mb-1">Course Attendance</h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Monitor class attendance rates, record daily roll call, and track student participation.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge py-2 px-3 rounded-pill bg-dark border border-secondary text-light" style="font-size: 0.88rem;">
                <i class="far fa-calendar-alt text-warning me-2"></i><?php echo date('l, F j, Y'); ?>
            </span>
        </div>
    </div>

    <?php if(!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important; color: #10b981;">
        <i class="fas fa-check-circle me-2"></i> <strong>Attendance Recorded:</strong> <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>



    <!-- Filter & Session Control Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="" id="filterForm" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-book-reader text-warning me-2"></i>Select Course</span>
                        <span class="small text-muted fw-normal">Choose to view roster</span>
                    </label>
                    <select class="form-select form-control" name="course_id" id="courseSelect" onchange="document.getElementById('filterForm').submit();" required>
                        <option value="" disabled <?php if($selected_course == '') echo 'selected'; ?>>Choose course to mark roll call...</option>
                        <?php foreach($courses_list as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($selected_course == $c['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($c['course_name']); ?> — Prof. <?php echo htmlspecialchars($c['teacher_name'] ?? 'Faculty'); ?> (<?php echo $c['enrolled_count']; ?> students)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0"><i class="far fa-calendar-alt text-warning me-2"></i>Session Date</label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="setQuickDate('<?php echo date('Y-m-d'); ?>')">Today</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="setQuickDate('<?php echo date('Y-m-d', strtotime('-1 day')); ?>')">Yesterday</button>
                        </div>
                    </div>
                    <input type="date" class="form-control" name="attendance_date" id="dateInput" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="document.getElementById('filterForm').submit();" required>
                </div>

                <div class="col-lg-3 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-users-viewfinder me-1"></i> Open Class Roster
                    </button>
                    <?php if($selected_course != ''): ?>
                    <a href="admin_attendance.php" class="btn btn-outline-secondary py-2" title="Reset view">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW 1: WHEN NO COURSE IS SELECTED (COMPREHENSIVE DASHBOARD OVERVIEW) -->
    <?php if($selected_course == ''): ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-1" style="font-size: 1.35rem;">
                    <i class="fas fa-layer-group text-warning me-2"></i>Active Academic Courses
                </h3>
                <p class="text-muted small mb-0">Select any course card below to instantly open roll call for <?php echo date('M d, Y', strtotime($selected_date)); ?>.</p>
            </div>
            <span class="badge py-2 px-3 rounded-pill bg-dark border border-secondary text-muted">
                <?php echo count($courses_list); ?> Courses Total
            </span>
        </div>

        <div class="row g-4 mb-5">
            <?php foreach($courses_list as $c): ?>
            <div class="col-lg-4 col-md-6">
                <div class="course-card">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge py-1 px-2 rounded" style="background: rgba(249,115,22,0.12); color: var(--accent-orange); font-size: 0.78rem; font-weight: 600;">
                                COURSE #<?php echo $c['id']; ?>
                            </span>
                            <?php if($c['today_marked'] > 0): ?>
                                <span class="badge py-1 px-2 rounded-pill bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">
                                    <i class="fas fa-check me-1"></i> Logged Today
                                </span>
                            <?php else: ?>
                                <span class="badge py-1 px-2 rounded-pill bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i> Pending Today
                                </span>
                            <?php endif; ?>
                        </div>

                        <h4 class="fw-bold mb-2" style="font-size: 1.2rem;"><?php echo htmlspecialchars($c['course_name']); ?></h4>
                        <div class="text-muted small mb-3">
                            <i class="fas fa-chalkboard-teacher text-warning me-1"></i> Instructor: <strong>Prof. <?php echo htmlspecialchars($c['teacher_name'] ?? 'Faculty'); ?></strong>
                        </div>

                        <div class="p-3 rounded mb-4" style="background: rgba(0,0,0,0.15); border: 1px solid var(--card-border);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Class Enrolled:</span>
                                <span class="fw-bold"><i class="fas fa-users me-1 text-info"></i> <?php echo $c['enrolled_count']; ?> Students</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Course Avg Rate:</span>
                                <span class="fw-bold <?php echo ($c['course_rate'] >= 75) ? 'text-success' : 'text-warning'; ?>">
                                    <?php echo ($c['course_rate'] !== null) ? $c['course_rate'] . '%' : 'N/A'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <a href="?course_id=<?php echo $c['id']; ?>&attendance_date=<?php echo htmlspecialchars($selected_date); ?>" class="btn btn-outline-warning w-100 py-2 fw-semibold">
                        <i class="fas fa-clipboard-check me-1"></i> Take Attendance <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>



    <!-- VIEW 2: WHEN A COURSE IS SELECTED (INTERACTIVE ROSTER & ROLL CALL) -->
    <?php else: ?>

        <!-- Selected Session Banner -->
        <div class="session-banner mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge py-1 px-3 rounded-pill mb-2" style="background: rgba(249,115,22,0.2); color: var(--accent-orange); font-size: 0.8rem; font-weight: 700;">
                        ACTIVE CLASS ROSTER
                    </span>
                    <h2 class="fw-bold mb-1" style="font-size: 1.6rem;">
                        <i class="fas fa-graduation-cap text-warning me-2"></i><?php echo htmlspecialchars($selected_course_info['course_name'] ?? 'Selected Course'); ?>
                    </h2>
                    <div class="text-muted d-flex flex-wrap gap-3 mt-1" style="font-size: 0.95rem;">
                        <span><i class="fas fa-chalkboard-teacher text-warning me-1"></i> Instructor: <strong>Prof. <?php echo htmlspecialchars($selected_course_info['teacher_name'] ?? 'Faculty'); ?></strong></span>
                        <span>&bull;</span>
                        <span><i class="far fa-calendar-alt text-info me-1"></i> Session Date: <strong><?php echo date('l, F j, Y', strtotime($selected_date)); ?></strong></span>
                        <span>&bull;</span>
                        <span><i class="fas fa-users text-success me-1"></i> Enrolled: <strong><?php echo count($enrolled_students); ?> Students</strong></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="admin_attendance.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Switch Course
                    </a>
                </div>
            </div>
        </div>

        <?php if(count($enrolled_students) > 0): ?>
        <form method="POST" action="" id="attendanceForm">
            <input type="hidden" name="course_id" value="<?php echo $selected_course; ?>">
            <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">

            <!-- Live Tally Toolbar & Bulk Actions -->
            <div class="card shadow-sm border-0 mb-4" style="background: var(--card-bg);">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <!-- Live Tally Chips -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="small text-muted fw-bold text-uppercase me-1">Live Status:</span>
                            <span class="badge py-2 px-3 rounded-pill bg-success-subtle text-success border border-success-subtle fs-6">
                                <i class="fas fa-check-circle me-1"></i> Present: <strong id="tallyPresent">0</strong>
                            </span>
                            <span class="badge py-2 px-3 rounded-pill bg-danger-subtle text-danger border border-danger-subtle fs-6">
                                <i class="fas fa-times-circle me-1"></i> Absent: <strong id="tallyAbsent">0</strong>
                            </span>
                            <span class="badge py-2 px-3 rounded-pill bg-secondary-subtle text-muted border border-secondary-subtle fs-6">
                                <i class="fas fa-question-circle me-1"></i> Unmarked: <strong id="tallyUnmarked"><?php echo count($enrolled_students); ?></strong>
                            </span>
                            <span class="badge py-2 px-3 rounded-pill bg-warning-subtle text-warning border border-warning-subtle fs-6">
                                <i class="fas fa-percentage me-1"></i> Rate: <strong id="tallyRate">0%</strong>
                            </span>
                        </div>

                        <!-- Quick Bulk Actions -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm px-3" onclick="bulkMark('Present')">
                                <i class="fas fa-check-double me-1"></i> Mark All Present
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="bulkMark('Absent')">
                                <i class="fas fa-times me-1"></i> Mark All Absent
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="resetRoster()">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Roster Table -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 70px;">#</th>
                                    <th>Student Details</th>
                                    <th>Course Attendance Standing</th>
                                    <th style="min-width: 280px;" class="text-center">Roll Call for <?php echo date('M d', strtotime($selected_date)); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $idx = 1;
                                foreach($enrolled_students as $s): 
                                    $words = explode(" ", trim($s['username']));
                                    $initials = "";
                                    foreach($words as $w) {
                                        if(!empty($w)) $initials .= strtoupper(substr($w, 0, 1));
                                    }
                                    $initials = substr($initials, 0, 2);
                                    if(empty($initials)) $initials = "ST";

                                    $s_total = (int)$s['student_total_classes'];
                                    $s_present = (int)$s['student_present_classes'];
                                    $s_pct = $s_total > 0 ? round(($s_present / $s_total) * 100, 1) : 0.0;
                                    $cur = $s['current_status'];
                                ?>
                                <tr class="student-row <?php echo ($cur == 'Present') ? 'row-present' : (($cur == 'Absent') ? 'row-absent' : ''); ?>" id="row_<?php echo $s['id']; ?>">
                                    <td class="text-muted fw-bold"><?php echo $idx++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="student-avatar"><?php echo $initials; ?></div>
                                            <div>
                                                <div class="fw-bold fs-6 text-heading"><?php echo htmlspecialchars($s['username']); ?></div>
                                                <div class="small text-muted"><i class="far fa-envelope me-1"></i><?php echo htmlspecialchars($s['email'] ?? 'student@aether.edu'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 220px;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small text-muted"><?php echo $s_present; ?> / <?php echo $s_total; ?> sessions</span>
                                                <span class="badge py-1 px-2 rounded-pill <?php echo ($s_pct >= 75) ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>" style="font-size: 0.78rem;">
                                                    <?php echo $s_pct; ?>%
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 6px; background-color: var(--card-border);">
                                                <div class="progress-bar <?php echo ($s_pct >= 75) ? 'bg-success' : 'bg-warning'; ?>" role="progressbar" style="width: <?php echo min(100, max(5, $s_pct)); ?>%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group w-100" role="group" style="max-width: 320px;">
                                            <input type="radio" class="btn-check attendance-radio present-radio" name="status[<?php echo $s['id']; ?>]" id="present_<?php echo $s['id']; ?>" value="Present" autocomplete="off" required <?php if($cur == 'Present') echo 'checked'; ?> onchange="onRadioChange(<?php echo $s['id']; ?>, 'Present')">
                                            <label class="btn btn-outline-success fw-bold py-2" for="present_<?php echo $s['id']; ?>">
                                                <i class="fas fa-check-circle me-1"></i> Present
                                            </label>

                                            <input type="radio" class="btn-check attendance-radio absent-radio" name="status[<?php echo $s['id']; ?>]" id="absent_<?php echo $s['id']; ?>" value="Absent" autocomplete="off" <?php if($cur == 'Absent') echo 'checked'; ?> onchange="onRadioChange(<?php echo $s['id']; ?>, 'Absent')">
                                            <label class="btn btn-outline-danger fw-bold py-2" for="absent_<?php echo $s['id']; ?>">
                                                <i class="fas fa-times-circle me-1"></i> Absent
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Bar -->
            <div class="sticky-save-bar d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-warning fs-5"></i>
                    <div>
                        <div class="fw-bold">Ready to finalize?</div>
                        <div class="small text-muted">Attendance records will instantly update student portal transcripts and standing.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="admin_attendance.php" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                    <button type="submit" name="save_attendance" class="btn btn-primary px-5 py-2 fw-bold shadow">
                        <i class="fas fa-save me-2"></i> Save & Confirm Attendance
                    </button>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="card shadow-sm border-0 p-5 text-center">
                <div class="text-warning mb-3" style="font-size: 3rem;"><i class="fas fa-user-slash"></i></div>
                <h4 class="fw-bold">No Students Enrolled</h4>
                <p class="text-muted">There are currently no students registered for this course. Enroll students from the Admissions or Student Management menu first.</p>
                <div class="mt-2">
                    <a href="admin_attendance.php" class="btn btn-outline-warning">
                        <i class="fas fa-arrow-left me-1"></i> Choose Another Course
                    </a>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<script>
function setQuickDate(dateStr) {
    document.getElementById('dateInput').value = dateStr;
    document.getElementById('filterForm').submit();
}

function onRadioChange(studentId, status) {
    let row = document.getElementById('row_' + studentId);
    if(row) {
        row.classList.remove('row-present', 'row-absent');
        if(status === 'Present') row.classList.add('row-present');
        if(status === 'Absent') row.classList.add('row-absent');
    }
    updateLiveTally();
}

function bulkMark(status) {
    $('.attendance-radio[value="' + status + '"]').each(function() {
        this.checked = true;
        let sId = this.id.split('_')[1];
        let row = document.getElementById('row_' + sId);
        if(row) {
            row.classList.remove('row-present', 'row-absent');
            if(status === 'Present') row.classList.add('row-present');
            if(status === 'Absent') row.classList.add('row-absent');
        }
    });
    updateLiveTally();
}

function resetRoster() {
    $('.attendance-radio').prop('checked', false);
    $('.student-row').removeClass('row-present row-absent');
    updateLiveTally();
}

function updateLiveTally() {
    let present = $('.present-radio:checked').length;
    let absent = $('.absent-radio:checked').length;
    let total = $('.student-row').length;
    let unmarked = total - (present + absent);
    let rate = (present + absent) > 0 ? Math.round((present / (present + absent)) * 100) : 0;

    $('#tallyPresent').text(present);
    $('#tallyAbsent').text(absent);
    $('#tallyUnmarked').text(unmarked);
    $('#tallyRate').text(rate + '%');
}

$(document).ready(function() {
    updateLiveTally();
});
</script>
    
</body>
</html>
