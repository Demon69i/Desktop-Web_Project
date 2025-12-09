<?php
require_once __DIR__ . '/config.php';

function getAllCourses() {
    $data = readJsonFile('courses.json');
    return $data ? $data['courses'] : [];
}

function getCourseById($id) {
    $courses = getAllCourses();
    foreach ($courses as $course) {
        if ($course['id'] == $id) {
            return $course;
        }
    }
    return null;
}

function createCourse($courseData) {
    $data = readJsonFile('courses.json');
    if (!$data) {
        $data = ['courses' => []];
    }
    
    $newId = 1;
    if (!empty($data['courses'])) {
        $maxId = max(array_column($data['courses'], 'id'));
        $newId = $maxId + 1;
    }
    
    $newCourse = [
        'id' => $newId,
        'title' => $courseData['title'],
        'category' => $courseData['category'],
        'category_color' => $courseData['category_color'] ?? '#2563EB',
        'thumbnail_url' => $courseData['thumbnail_url'],
        'short_description' => $courseData['short_description'],
        'long_description' => $courseData['long_description'],
        'instructor_name' => $courseData['instructor_name'],
        'difficulty_level' => $courseData['difficulty_level'],
        'created_at' => date('Y-m-d H:i:s'),
        'lessons' => $courseData['lessons'] ?? []
    ];
    
    $data['courses'][] = $newCourse;
    
    if (writeJsonFile('courses.json', $data)) {
        return ['success' => true, 'course' => $newCourse];
    }
    
    return ['success' => false, 'message' => 'Failed to create course'];
}

function updateCourse($id, $courseData) {
    $data = readJsonFile('courses.json');
    if (!$data) return ['success' => false, 'message' => 'No courses found'];
    
    foreach ($data['courses'] as &$course) {
        if ($course['id'] == $id) {
            $course['title'] = $courseData['title'] ?? $course['title'];
            $course['category'] = $courseData['category'] ?? $course['category'];
            $course['category_color'] = $courseData['category_color'] ?? $course['category_color'];
            $course['thumbnail_url'] = $courseData['thumbnail_url'] ?? $course['thumbnail_url'];
            $course['short_description'] = $courseData['short_description'] ?? $course['short_description'];
            $course['long_description'] = $courseData['long_description'] ?? $course['long_description'];
            $course['instructor_name'] = $courseData['instructor_name'] ?? $course['instructor_name'];
            $course['difficulty_level'] = $courseData['difficulty_level'] ?? $course['difficulty_level'];
            $course['lessons'] = $courseData['lessons'] ?? $course['lessons'];
            
            if (writeJsonFile('courses.json', $data)) {
                return ['success' => true, 'course' => $course];
            }
        }
    }
    
    return ['success' => false, 'message' => 'Course not found'];
}

function deleteCourse($id) {
    $data = readJsonFile('courses.json');
    if (!$data) return ['success' => false, 'message' => 'No courses found'];
    
    $initialCount = count($data['courses']);
    $data['courses'] = array_filter($data['courses'], function($course) use ($id) {
        return $course['id'] != $id;
    });
    $data['courses'] = array_values($data['courses']);
    
    if (count($data['courses']) < $initialCount) {
        if (writeJsonFile('courses.json', $data)) {
            return ['success' => true];
        }
    }
    
    return ['success' => false, 'message' => 'Course not found'];
}

function getUserEnrollments($userId) {
    $data = readJsonFile('enrollments.json');
    if (!$data) return [];
    
    $enrollments = array_filter($data['enrollments'], function($enrollment) use ($userId) {
        return $enrollment['user_id'] == $userId;
    });
    
    return array_values($enrollments);
}

function enrollUserInCourse($userId, $courseId) {
    $data = readJsonFile('enrollments.json');
    if (!$data) {
        $data = ['enrollments' => []];
    }
    
    foreach ($data['enrollments'] as $enrollment) {
        if ($enrollment['user_id'] == $userId && $enrollment['course_id'] == $courseId) {
            return ['success' => false, 'message' => 'Already enrolled'];
        }
    }
    
    $newId = 1;
    if (!empty($data['enrollments'])) {
        $maxId = max(array_column($data['enrollments'], 'id'));
        $newId = $maxId + 1;
    }
    
    $newEnrollment = [
        'id' => $newId,
        'user_id' => $userId,
        'course_id' => $courseId,
        'progress' => 0,
        'enrolled_at' => date('Y-m-d H:i:s'),
        'completed_lessons' => []
    ];
    
    $data['enrollments'][] = $newEnrollment;
    
    if (writeJsonFile('enrollments.json', $data)) {
        return ['success' => true, 'enrollment' => $newEnrollment];
    }
    
    return ['success' => false, 'message' => 'Failed to enroll'];
}

function completeLesson($userId, $courseId, $lessonId) {
    $data = readJsonFile('enrollments.json');
    if (!$data) {
        return ['success' => false, 'message' => 'No enrollments found'];
    }
    
    $course = getCourseById($courseId);
    if (!$course) {
        return ['success' => false, 'message' => 'Course not found'];
    }
    
    $totalLessons = count($course['lessons'] ?? []);
    if ($totalLessons == 0) {
        return ['success' => false, 'message' => 'No lessons in course'];
    }
    
    foreach ($data['enrollments'] as &$enrollment) {
        if ($enrollment['user_id'] == $userId && $enrollment['course_id'] == $courseId) {
            if (!in_array($lessonId, $enrollment['completed_lessons'])) {
                $enrollment['completed_lessons'][] = $lessonId;
            }
            
            $completedCount = count($enrollment['completed_lessons']);
            $enrollment['progress'] = round(($completedCount / $totalLessons) * 100);
            
            if (writeJsonFile('enrollments.json', $data)) {
                return ['success' => true, 'progress' => $enrollment['progress'], 'completed_lessons' => $enrollment['completed_lessons']];
            }
        }
    }
    
    return ['success' => false, 'message' => 'Enrollment not found'];
}

function uncompleteLesson($userId, $courseId, $lessonId) {
    $data = readJsonFile('enrollments.json');
    if (!$data) {
        return ['success' => false, 'message' => 'No enrollments found'];
    }
    
    $course = getCourseById($courseId);
    if (!$course) {
        return ['success' => false, 'message' => 'Course not found'];
    }
    
    $totalLessons = count($course['lessons'] ?? []);
    
    foreach ($data['enrollments'] as &$enrollment) {
        if ($enrollment['user_id'] == $userId && $enrollment['course_id'] == $courseId) {
            $enrollment['completed_lessons'] = array_values(array_filter($enrollment['completed_lessons'], function($id) use ($lessonId) {
                return $id != $lessonId;
            }));
            
            $completedCount = count($enrollment['completed_lessons']);
            $enrollment['progress'] = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
            
            if (writeJsonFile('enrollments.json', $data)) {
                return ['success' => true, 'progress' => $enrollment['progress'], 'completed_lessons' => $enrollment['completed_lessons']];
            }
        }
    }
    
    return ['success' => false, 'message' => 'Enrollment not found'];
}
?>
