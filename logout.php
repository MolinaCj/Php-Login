<?php
ob_start();
session_start();

if (isset($_SESSION['user_login_id'])) {
    // Unset specific session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_mobile']);
    session_destroy(); // Optional: Use if you want to destroy all session data

    header("Location: login.php"); // Redirect to login page after logout
    exit();
} else {
    // If user is not logged in, redirect to dashboard or appropriate page
    header("Location: dashboard.php");
    exit();
}
