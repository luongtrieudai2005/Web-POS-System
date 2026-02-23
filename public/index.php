<?php

// Load cac file config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// Load cac core class
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/Validator.php';

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
        Router::redirect(Router::url('dashboard'));
    } else {
        Router::redirect(Router::url('login'));
    }
});

// ===== AUTH ROUTES =====

// Login - GET (hien form)
$router->get('/login', function() {
    require __DIR__ . '/login.php';
});

// Login - POST (xu ly submit)
$router->post('/login', function() {
    require __DIR__ . '/login.php';
});

// Logout
$router->get('/logout', function() {
    Auth::logout();
    Session::setFlash('success', 'Da dang xuat thanh cong!', 'success');
    Router::redirect(Router::url('login'));
});

// First login - GET
$router->get('/first-login', function() {
    require __DIR__ . '/first-login.php';
});

// First login - POST
$router->post('/first-login', function() {
    require __DIR__ . '/first-login.php';
});

// ===== PROTECTED ROUTES =====

// Dashboard (trang chu sau khi login)
$router->get('/dashboard', function() {
    require __DIR__ . '/dashboard.php';
});

// Profile
$router->get('/profile', function() {
    Auth::requireLogin();
    require __DIR__ . '/profile.php';
});

// ===== ADMIN ONLY ROUTES =====

// Users management
$router->get('/users', function() {
    Auth::requireAdmin();
    require __DIR__ . '/users/index.php';
});

$router->get('/users/create', function() {
    Auth::requireAdmin();
    require __DIR__ . '/users/create.php';
});

$router->post('/users/create', function() {
    Auth::requireAdmin();
    require __DIR__ . '/users/create.php';
});

// Categories
$router->get('/categories', function() {
    Auth::requireAdmin();
    require __DIR__ . '/categories/index.php';
});

// ===== EMPLOYEE & ADMIN ROUTES =====

// Products
$router->get('/products', function() {
    Auth::requireLogin();
    require __DIR__ . '/products/index.php';
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

// ===== 404 HANDLER =====
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