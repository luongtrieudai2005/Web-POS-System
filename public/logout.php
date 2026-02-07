<?php
/**
 * Logout Page
 * Dang xuat khoi he thong
 */

// Load bootstrap
require_once __DIR__ . '/../config/bootstrap.php';

// Dang xuat
Auth::logout();

// Redirect ve trang login
Session::setFlash('success', 'Bạn đã đăng xuất thành công!', 'info');
Router::redirect(Router::url('login.php'));
?>