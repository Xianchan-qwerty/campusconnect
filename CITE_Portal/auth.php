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

        // FIXED (Unified session format)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        return true;
    }
    return false;
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: admin_login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        if (!($_SESSION['role'] === 'admin' && $role === 'teacher')) {
            die("Access denied.");
        }
    }
}

function logout() {
    $_SESSION = [];
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

function current_user() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ];
}
?>
