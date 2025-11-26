<?php
// auth.php
require_once 'config.php';

function login($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if ($user && hash('sha256', $password) === $user['password_hash']) {
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'name'     => $user['name'],
            'role'     => $user['role'],
        ];
        return true;
    }
    return false;
}

function require_login() {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        // Allow admin to access teacher pages
        if (!($_SESSION['user']['role'] === 'admin' && $role === 'teacher')) {
            die("Access denied.");
        }
    }
}

function logout() {
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header('Location: login.php');
    exit;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}
?>
