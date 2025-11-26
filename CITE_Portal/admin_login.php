<?php
// admin_login.php
require_once 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password && login($username, $password)) {
        $user = current_user();

        if ($user['role'] !== 'admin') {
            $error = "Admin access only.";
            logout();
        } else {
            header("Location: admin_panel.php");
            exit;
        }
    } else {
        $error = "Invalid admin username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-body">

<div class="auth-container">
    <h1>Admin Login</h1>

    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Admin Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
