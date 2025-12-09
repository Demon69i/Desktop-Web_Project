<?php
require_once __DIR__ . '/config.php';

function authenticateUser($email, $password) {
    $users = readJsonFile('users.json');
    if (!$users) return false;
    
    foreach ($users['users'] as $user) {
        if ($user['email'] === $email) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                return $user;
            }
        }
    }
    return false;
}

function registerUser($email, $password, $name, $role = 'student') {
    $role = 'student';
    
    $users = readJsonFile('users.json');
    if (!$users) {
        $users = ['users' => []];
    }
    
    foreach ($users['users'] as $user) {
        if ($user['email'] === $email) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
    }
    
    $newId = 1;
    if (!empty($users['users'])) {
        $maxId = max(array_column($users['users'], 'id'));
        $newId = $maxId + 1;
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $newUser = [
        'id' => $newId,
        'email' => $email,
        'password' => $hashedPassword,
        'name' => $name,
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $users['users'][] = $newUser;
    
    if (writeJsonFile('users.json', $users)) {
        return ['success' => true, 'user' => $newUser];
    }
    
    return ['success' => false, 'message' => 'Failed to create user'];
}

function logout() {
    session_destroy();
    header('Location: /index.php');
    exit;
}
?>
