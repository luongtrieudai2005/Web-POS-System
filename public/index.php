<?php
/**
 * Entry Point - Bootstrap File
 * File nay la diem khoi dau cua ung dung
 */

// Load cac file config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// Load cac core class
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Mailer.php';
require_once __DIR__ . '/../core/Helper.php';

// Khoi tao session
Session::start();

// Tao router instance
$router = new Router();

// ===================================
// DINH NGHIA ROUTES
// ===================================

// Trang chu - Redirect den dashboard hoac login
$router->get('/', function() {
    if (Auth::check()) {
        Router::redirect(Router::url('dashboard.php'));
    } else {
        Router::redirect(Router::url('login.php'));
    }
});

// Trang login
$router->get('/login', function() {
    require __DIR__ . '/login.php';
});

// Xu ly login (POST)
$router->post('/login', function() {
    require __DIR__ . '/login.php';
});

// Logout
$router->get('/logout', function() {
    Auth::logout();
    Router::redirect(Router::url('login.php'));
});

// Dashboard (trang chu sau khi login)
$router->get('/dashboard', function() {
    Auth::requireLogin();
    require __DIR__ . '/dashboard.php';
});

// First login - Doi mat khau lan dau
$router->get('/first-login', function() {
    Auth::requireLogin();
    require __DIR__ . '/first-login.php';
});

// Profile
$router->get('/profile', function() {
    Auth::requireLogin();
    require __DIR__ . '/profile.php';
});

// Users management (Admin only)
$router->get('/users', function() {
    Auth::requireAdmin();
    require __DIR__ . '/users/index.php';
});

$router->get('/users/create', function() {
    Auth::requireAdmin();
    require __DIR__ . '/users/create.php';
});

// Products
$router->get('/products', function() {
    Auth::requireLogin();
    require __DIR__ . '/products/index.php';
});

// Categories (Admin only)
$router->get('/categories', function() {
    Auth::requireAdmin();
    require __DIR__ . '/categories/index.php';
});

// Transactions (POS)
$router->get('/transactions', function() {
    Auth::requireLogin();
    require __DIR__ . '/transactions/index.php';
});

// Reports
$router->get('/reports', function() {
    Auth::requireLogin();
    require __DIR__ . '/reports/index.php';
});

// 404 Handler
$router->setNotFound(function() {
    require __DIR__ . '/404.php';
});

// ===================================
// XU LY REQUEST
// ===================================

try {
    $router->dispatch();
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>