<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/courses.php';

requireLogin();

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = getCourseById($courseId);

if (!$course) {
    header('Location: /student/dashboard.php');
    exit;
}

$enrollments = getUserEnrollments($_SESSION['user_id']);
$isEnrolled = false;
$enrollment = null;

foreach ($enrollments as $e) {
    if ($e['course_id'] == $courseId) {
        $isEnrolled = true;
        $enrollment = $e;
        break;
    }
}

function getYoutubeVideoId($url) {
    if (empty($url)) return null;
    
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($course['title']); ?> - CyberLearn</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo-dot"></span>
            <span>CyberLearn</span>
        </div>
        <div class="navbar-nav" style="margin-left: auto;">
            <a href="/student/dashboard.php">All Courses</a>
            <a href="/student/my-courses.php">My Courses</a>
            <span class="user-name"><?php echo sanitize($_SESSION['name']); ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="course-detail-container">
        <div class="course-detail-header">
            <span class="category-badge" style="background-color: <?php echo $course['category_color']; ?>20; color: <?php echo $course['category_color']; ?>;">
                <?php echo sanitize($course['category']); ?>
            </span>
            <h1><?php echo sanitize($course['title']); ?></h1>
            <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">
                <?php echo sanitize($course['long_description'] ?: $course['short_description']); ?>
            </p>
        </div>

        <div class="course-info-grid">
            <div class="info-card">
                <h4>Instructor</h4>
                <p><?php echo sanitize($course['instructor_name']); ?></p>
            </div>
            <div class="info-card">
                <h4>Difficulty</h4>
                <p><?php echo sanitize($course['difficulty_level']); ?></p>
            </div>
            <div class="info-card">
                <h4>Lessons</h4>
                <p><?php echo count($course['lessons'] ?? []); ?> Lessons</p>
            </div>
        </div>

        <?php if (!$isEnrolled): ?>
            <div style="text-align: center; margin-bottom: 32px;">
                <button class="btn btn-primary" onclick="enrollCourse(<?php echo $course['id']; ?>)" style="padding: 16px 48px; font-size: 16px;">
                    Enroll in This Course
                </button>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 32px;" id="progressContainer">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="color: #94a3b8;">Your Progress</span>
                    <span style="color: var(--cyan);" id="progressText"><?php echo $enrollment['progress']; ?>%</span>
                </div>
                <div class="progress-bar" style="height: 12px;">
                    <div class="progress-fill" id="progressBar" style="width: <?php echo $enrollment['progress']; ?>%"></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="lessons-section">
            <h2>Course Content</h2>
            <?php if ($isEnrolled): ?>
                <p style="color: #94a3b8; font-size: 13px; margin-bottom: 16px;">Click on a lesson to mark it as complete</p>
            <?php endif; ?>
            
            <?php if (empty($course['lessons'])): ?>
                <p style="color: #64748b; text-align: center; padding: 40px;">No lessons available yet.</p>
            <?php else: ?>
                <?php foreach ($course['lessons'] as $index => $lesson): 
                    $isCompleted = $isEnrolled && in_array($lesson['id'], $enrollment['completed_lessons'] ?? []);
                    $videoId = getYoutubeVideoId($lesson['video_url'] ?? '');
                ?>
                    <div class="lesson-card" data-lesson-id="<?php echo $lesson['id']; ?>">
                        <div class="lesson-list-item <?php echo $isCompleted ? 'lesson-completed' : ''; ?>" 
                             data-lesson-id="<?php echo $lesson['id']; ?>"
                             data-course-id="<?php echo $course['id']; ?>"
                             data-completed="<?php echo $isCompleted ? 'true' : 'false'; ?>"
                             <?php if ($isEnrolled): ?>onclick="toggleLessonComplete(this)"<?php endif; ?>
                             style="<?php echo $isEnrolled ? 'cursor: pointer;' : ''; ?>">
                            <div class="lesson-info">
                                <span class="lesson-number"><?php echo $isCompleted ? '✓' : ($index + 1); ?></span>
                                <div>
                                    <div class="lesson-title"><?php echo sanitize($lesson['title']); ?></div>
                                    <?php if (!empty($lesson['content'])): ?>
                                        <div style="color: #64748b; font-size: 13px; margin-top: 4px;">
                                            <?php echo sanitize(substr($lesson['content'], 0, 100)); ?>...
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($videoId && $isEnrolled): ?>
                                        <div style="color: var(--cyan); font-size: 12px; margin-top: 4px;">
                                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                                Video Available
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="lesson-duration"><?php echo sanitize($lesson['duration'] ?? ''); ?></span>
                                <?php if ($isEnrolled): ?>
                                    <span class="lesson-status" style="font-size: 12px; color: <?php echo $isCompleted ? 'var(--secondary)' : '#64748b'; ?>;">
                                        <?php echo $isCompleted ? 'Completed' : 'Mark Complete'; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($videoId && $isEnrolled): ?>
                            <div class="lesson-video-container" style="margin-top: 12px; border-radius: 8px; overflow: hidden;">
                                <iframe 
                                    width="100%" 
                                    height="400" 
                                    src="https://www.youtube.com/embed/<?php echo $videoId; ?>" 
                                    title="<?php echo sanitize($lesson['title']); ?>"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen
                                    style="display: block; background: #000;">
                                </iframe>
                            </div>
                        <?php elseif (!empty($lesson['video_url']) && !$isEnrolled): ?>
                            <div class="lesson-video-locked" style="margin-top: 12px; padding: 40px; background: var(--dark-bg); border-radius: 8px; text-align: center;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#64748b" style="margin-bottom: 12px;">
                                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                </svg>
                                <p style="color: #64748b; font-size: 14px;">Enroll in this course to watch the video</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 32px;">
            <a href="/student/dashboard.php" class="btn btn-secondary">← Back to Courses</a>
        </div>
    </div>

    <footer class="footer">
        &copy; <?php echo date('Y'); ?> CyberLearn. All rights reserved.
    </footer>

    <script src="/assets/js/student.js"></script>
</body>
</html>
