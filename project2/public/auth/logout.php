<?php
//  User logout page

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

//  Clear all session data
$_SESSION = [];

//  Delete session cookie if it exists
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

//  Destroy the session completely
session_destroy();

//  Redirect user to login page
header('Location: ' . base_url('/auth/login.php'));
exit;
?>
