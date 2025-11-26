<?php
// header.php
require_once 'auth.php';
$user = current_user();
?>
<header class="topbar">
    <div class="topbar-left">
        <div class="logo-circle">
          <img src="assets/campusss.png" class="site-logo-img" alt="CampusConnect Logo">
        </div>
        <div class="topbar-text">
            <div class="topbar-title">CampusConnect</div>
            <div class="topbar-subtitle">Communication Platform</div>
        </div>
    </div>

    <!-- No login button shown -->
    <div class="topbar-right">
        <!-- Empty space -->
    </div>
</header>
