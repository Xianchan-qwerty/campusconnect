<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles.css">

<div class="auth-body">
    <div class="auth-container">
        <h1>Faculty Signup</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">Username already exists</div>
        <?php endif; ?>

        <form action="faculty_signup_process.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>
    </div>
</div>
