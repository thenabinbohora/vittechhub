<?php
//  Helper functions

//  Check if a user is logged in
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

//  Check if the current user is an admin
function is_admin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

//  Require login for protected pages
function require_login(): void {
    if (!is_logged_in()) {
        header("Location: " . base_url('/auth/login.php'));
        exit;
    }
}

//  Require admin role for admin-only pages
function require_admin(): void {
    if (!is_admin()) {
        header("Location: " . base_url('/auth/login.php'));
        exit;
    }
}

//  Escape HTML output to prevent XSS attacks
function h($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

//  Generate a base URL for internal links
function base_url(string $path = ''): string {
    $conf = require __DIR__ . '/config.php';
    $base = rtrim($conf['base_path'] ?? '', '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}
