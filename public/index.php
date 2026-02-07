<?php
/**
 * Index Page - Entry Point
 * Redirect den trang phu hop
 */

// Load bootstrap
require_once __DIR__ . '/../config/bootstrap.php';

// Redirect logic
if (Auth::check()) {
    // Da dang nhap
    if (Auth::requirePasswordChange()) {
        // Can doi mat khau
        Router::redirect(Router::url('first-login.php'));
    } else {
        // Vao dashboard
        Router::redirect(Router::url('dashboard.php'));
    }
} else {
    // Chua dang nhap -> trang login
    Router::redirect(Router::url('login.php'));
}
?>