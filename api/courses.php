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
        if (isset($_GET['id'])) {
            $course = getCourseById($_GET['id']);
            if ($course) {
                echo json_encode(['success' => true, 'course' => $course]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Course not found']);
            }
        } else {
            $courses = getAllCourses();
            echo json_encode(['success' => true, 'courses' => $courses]);
        }
        break;
        
    case 'POST':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['title']) || empty($input['title'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course title is required']);
            exit;
        }
        
        $result = createCourse($input);
        echo json_encode($result);
        break;
        
    case 'PUT':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course ID is required']);
            exit;
        }
        
        $result = updateCourse($input['id'], $input);
        echo json_encode($result);
        break;
        
    case 'DELETE':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course ID is required']);
            exit;
        }
        
        $result = deleteCourse($input['id']);
        echo json_encode($result);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
