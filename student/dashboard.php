<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/courses.php';

requireLogin();

$courses = getAllCourses();
$enrollments = getUserEnrollments($_SESSION['user_id']);
$enrolledCourseIds = array_column($enrollments, 'course_id');
$enrollmentMap = [];
foreach ($enrollments as $enrollment) {
    $enrollmentMap[$enrollment['course_id']] = $enrollment;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberLearn - Student Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo-dot"></span>
            <span>CyberLearn</span>
        </div>
        <div class="navbar-search">
            <div class="search-wrapper">
                <input type="text" id="searchInput" placeholder="Search for courses...">
            </div>
        </div>
        <div class="navbar-nav">
            <a href="/student/dashboard.php" class="active">All Courses</a>
            <a href="/student/my-courses.php">My Courses</a>
            <span class="user-name"><?php echo sanitize($_SESSION['name']); ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Browse Courses</h1>
        </div>

        <div class="courses-grid" id="coursesGrid">
            <?php foreach ($courses as $course): 
                $isEnrolled = in_array($course['id'], $enrolledCourseIds);
                $progress = isset($enrollmentMap[$course['id']]) ? $enrollmentMap[$course['id']]['progress'] : 0;
            ?>
            <div class="course-card student-course-card" data-course-id="<?php echo $course['id']; ?>" onclick="viewCourse(<?php echo $course['id']; ?>)">
                <div class="course-header" style="background-color: <?php echo $course['category_color']; ?>20; color: <?php echo $course['category_color']; ?>;">
                    <?php echo sanitize($course['category']); ?>
                    <?php if ($isEnrolled): ?>
                        <span class="enrolled-badge">Enrolled</span>
                    <?php endif; ?>
                </div>
                <div class="course-body">
                    <h3 class="course-title"><?php echo sanitize($course['title']); ?></h3>
                    <p class="course-description"><?php echo sanitize($course['short_description']); ?></p>
                    <div class="course-meta">
                        <span>👤 <?php echo sanitize($course['instructor_name']); ?></span>
                        <span>📊 <?php echo sanitize($course['difficulty_level']); ?></span>
                    </div>
                    <?php if ($isEnrolled): ?>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;"><?php echo $progress; ?>% Complete</p>
                    <?php else: ?>
                        <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); enrollCourse(<?php echo $course['id']; ?>)">
                            Enroll Now
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="footer">
        &copy; <?php echo date('Y'); ?> CyberLearn. All rights reserved.
    </footer>

    <script src="/assets/js/student.js"></script>
</body>
</html>
