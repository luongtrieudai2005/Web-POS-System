<?php
/**
 * Login Page
 * Xu ly ca dang nhap binh thuong va dang nhap qua token
 */

// Neu da dang nhap roi thi redirect ve dashboard
if (Auth::check()) {
    Router::redirect(Router::url('dashboard.php'));
    exit;
}

$errors = [];
$username = '';
$token = Helper::get('token', '');

// XU LY DANG NHAP QUA TOKEN (Nhan vien moi)
if (!empty($token) && Helper::isGet()) {
    try {
        $user = Auth::loginWithToken($token);
        
        if ($user) {
            // Dang nhap thanh cong, redirect den trang doi mat khau
            Session::setFlash('success', 'Dang nhap thanh cong! Vui long tao mat khau moi.', 'success');
            Router::redirect(Router::url('first-login.php'));
            exit;
        } else {
            $errors['token'] = 'Lien ket dang nhap khong hop le hoac da het han. Vui long lien he quan tri vien de gui lai email.';
        }
    } catch (Exception $e) {
        $errors['token'] = $e->getMessage();
    }
}

// XU LY DANG NHAP BINH THUONG (POST)
if (Helper::isPost()) {
    $username = Helper::post('username', '');
    $password = Helper::post('password', '');
    
    // Validation
    $validator = new Validator($_POST);
    $validator->validate([
        'username' => 'required',
        'password' => 'required'
    ]);
    
    if ($validator->fails()) {
        $errors = $validator->errors();
    } else {
        try {
            // Kiem tra xem co phai nhan vien moi chua dang nhap lan dau khong
            $db = Database::getInstance();
            $checkUser = $db->fetchOne(
                "SELECT is_first_login FROM users WHERE username = ?",
                [$username]
            );
            
            // Neu la nhan vien moi (is_first_login = 1) thi KHONG cho phep dang nhap truc tiep
            if ($checkUser && $checkUser['is_first_login'] == 1) {
                $errors['username'] = 'Vui long dang nhap bang cach nhan vao lien ket trong email cua ban.';
            } else {
                // Dang nhap binh thuong
                $user = Auth::login($username, $password);
                
                if ($user) {
                    // Dang nhap thanh cong
                    Session::setFlash('success', 'Chao mung ' . $user['full_name'] . '!', 'success');
                    
                    // Redirect den dashboard
                    Router::redirect(Router::url('dashboard.php'));
                    exit;
                } else {
                    $errors['login'] = 'Ten dang nhap hoac mat khau khong chinh xac.';
                }
            }
        } catch (Exception $e) {
            $errors['login'] = $e->getMessage();
        }
    }
}

// Hien thi view
require_once __DIR__ . '/../views/auth/login.php';
?>