<?php
// header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="topbar">
    <div class="topbar-left">
        <img src="assets/campusss.png" class="site-logo-img">

        <div class="topbar-text">
            <div class="topbar-title">CampusConnect</div>
            <div class="topbar-subtitle">Communication Platform</div>
        </div>
    </div>

    <!-- RIGHT SIDE COMPLETELY EMPTY -->
    <div class="topbar-right"></div>
</header>
