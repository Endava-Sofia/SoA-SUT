<?php
session_start();

// If user is not logged in, redirect to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
