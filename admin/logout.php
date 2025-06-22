<?php
// Admin logout script
require_once '../includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the logout action
if (isset($_SESSION['admin_username'])) {
    log_error("Admin logout: " . $_SESSION['admin_username']);
}

// Destroy all session data
$_SESSION = array();

// Delete session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
redirect('login.php?logged_out=1');
?>