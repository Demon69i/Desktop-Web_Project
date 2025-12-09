<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/courses.php';

requireAdmin();

$courses = getAllCourses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberLearn - Admin Dashboard</title>
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
            <a href="/admin/dashboard.php" class="active">Courses</a>
            <!-- <a href="/admin/dashboard.php">Admin</a> -->
            <span class="user-name"><?php echo sanitize($_SESSION['name']); ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Manage Courses</h1>
            <button class="btn btn-add-course" onclick="openAddCourseModal()">
                <span>+</span> Add New Course
            </button>
        </div>

        <div class="courses-grid" id="coursesGrid">
            <?php foreach ($courses as $course): ?>
            <div class="course-card" data-course-id="<?php echo $course['id']; ?>">
                <div class="course-header" style="background-color: <?php echo $course['category_color']; ?>20; color: <?php echo $course['category_color']; ?>;">
                    <?php echo sanitize($course['category']); ?>
                </div>
                <div class="course-body">
                    <h3 class="course-title"><?php echo sanitize($course['title']); ?></h3>
                    <p class="course-description"><?php echo sanitize($course['short_description']); ?></p>
                    <div class="course-actions">
                        <button class="btn btn-secondary btn-sm" onclick="openEditCourseModal(<?php echo $course['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCourse(<?php echo $course['id']; ?>)">
                            🗑️ Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="footer">
        &copy; <?php echo date('Y'); ?> CyberLearn. All rights reserved.
    </footer>

    <div class="modal-overlay" id="courseModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Course</h2>
                <button class="modal-close" onclick="closeModal()">Cancel</button>
            </div>
            <form id="courseForm">
                <div class="modal-body">
                    <input type="hidden" id="courseId" name="id">
                    
                    <div class="form-group">
                        <label for="courseTitle">Course Title</label>
                        <input type="text" id="courseTitle" name="title" placeholder="Course Title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="courseCategory">Category</label>
                        <input type="text" id="courseCategory" name="category" placeholder="e.g., Web Security, Cyber Basics" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoryColor">Category Color</label>
                        <select id="categoryColor" name="category_color">
                            <option value="#f97316">Orange</option>
                            <option value="#a855f7">Purple</option>
                            <option value="#14b8a6">Teal</option>
                            <option value="#22c55e">Green</option>
                            <option value="#2563EB">Blue</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="thumbnailUrl">Thumbnail URL</label>
                        <input type="url" id="thumbnailUrl" name="thumbnail_url" placeholder="https://placehold.co/600x400">
                    </div>
                    
                    <div class="form-group">
                        <label for="shortDescription">Short Description</label>
                        <textarea id="shortDescription" name="short_description" rows="2" placeholder="A short description for the new course."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="longDescription">Long Description</label>
                        <textarea id="longDescription" name="long_description" rows="4" placeholder="A detailed description for the course."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="instructorName">Instructor Name</label>
                        <input type="text" id="instructorName" name="instructor_name" placeholder="New Instructor">
                    </div>
                    
                    <div class="form-group">
                        <label for="difficultyLevel">Difficulty Level</label>
                        <select id="difficultyLevel" name="difficulty_level">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Lessons</label>
                        <div id="lessonsContainer"></div>
                        <button type="button" class="add-lesson-btn" onclick="addLesson()">
                            + Add Lesson
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        💾 Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/admin.js"></script>
</body>
</html>
