<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/courses.php';

requireLogin();

$enrollments = getUserEnrollments($_SESSION['user_id']);
$courses = getAllCourses();

$enrolledCourses = [];
foreach ($enrollments as $enrollment) {
    foreach ($courses as $course) {
        if ($course['id'] == $enrollment['course_id']) {
            $course['progress'] = $enrollment['progress'];
            $course['enrolled_at'] = $enrollment['enrolled_at'];
            $enrolledCourses[] = $course;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberLearn - My Courses</title>
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
                <input type="text" id="searchInput" placeholder="Search your courses...">
            </div>
        </div>
        <div class="navbar-nav">
            <a href="/student/dashboard.php">All Courses</a>
            <a href="/student/my-courses.php" class="active">My Courses</a>
            <span class="user-name"><?php echo sanitize($_SESSION['name']); ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>My Courses</h1>
        </div>

        <?php if (empty($enrolledCourses)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <h2 style="color: #94a3b8; margin-bottom: 16px;">No Courses Yet</h2>
                <p style="color: #64748b; margin-bottom: 24px;">You haven't enrolled in any courses yet. Start learning today!</p>
                <a href="/student/dashboard.php" class="btn btn-primary">Browse Courses</a>
            </div>
        <?php else: ?>
            <div class="courses-grid" id="coursesGrid">
                <?php foreach ($enrolledCourses as $course): ?>
                <div class="course-card student-course-card" data-course-id="<?php echo $course['id']; ?>" onclick="viewCourse(<?php echo $course['id']; ?>)">
                    <div class="course-header" style="background-color: <?php echo $course['category_color']; ?>20; color: <?php echo $course['category_color']; ?>;">
                        <?php echo sanitize($course['category']); ?>
                    </div>
                    <div class="course-body">
                        <h3 class="course-title"><?php echo sanitize($course['title']); ?></h3>
                        <p class="course-description"><?php echo sanitize($course['short_description']); ?></p>
                        <div class="course-meta">
                            <span>👤 <?php echo sanitize($course['instructor_name']); ?></span>
                            <span>📊 <?php echo sanitize($course['difficulty_level']); ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $course['progress']; ?>%"></div>
                        </div>
                        <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;"><?php echo $course['progress']; ?>% Complete</p>
                        <button class="btn btn-primary btn-sm" style="margin-top: 12px;" onclick="event.stopPropagation(); viewCourse(<?php echo $course['id']; ?>)">
                            Continue Learning
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        &copy; <?php echo date('Y'); ?> CyberLearn. All rights reserved.
    </footer>

    <script src="/assets/js/student.js"></script>
</body>
</html>
