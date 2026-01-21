<?php
// includes/auth.php

function require_user_login(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin_login(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

