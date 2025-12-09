<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/courses.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $userId = $_SESSION['user_id'];
        $enrollments = getUserEnrollments($userId);
        echo json_encode(['success' => true, 'enrollments' => $enrollments]);
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['course_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course ID is required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $result = enrollUserInCourse($userId, $input['course_id']);
        echo json_encode($result);
        break;
    
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['course_id']) || !isset($input['lesson_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course ID and Lesson ID are required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $action = $input['action'] ?? 'complete';
        
        if ($action === 'uncomplete') {
            $result = uncompleteLesson($userId, $input['course_id'], $input['lesson_id']);
        } else {
            $result = completeLesson($userId, $input['course_id'], $input['lesson_id']);
        }
        
        echo json_encode($result);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
