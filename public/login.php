<?php
/**
 * Login Page
 * File nay duoc goi tu Router, KHONG CAN require bootstrap
 */

// Neu da dang nhap roi thi redirect ve dashboard
if (Auth::check()) {
    Router::redirect(Router::url('dashboard'));
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
            Session::setFlash('success', 'Dang nhap thanh cong! Vui long tao mat khau moi.', 'success');
            Router::redirect(Router::url('first-login'));
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
        'username' => 'required|min:3|max:50',
        'password' => 'required|min:3'
    ]);
    
    if ($validator->fails()) {
        $errors = $validator->errors();
    } else {
        try {
            // Kiem tra nhan vien moi chua dang nhap lan dau
            $db = Database::getInstance();
            $checkUser = $db->fetchOne(
                "SELECT is_first_login FROM users WHERE username = ?",
                [$username]
            );
            
            // Neu la nhan vien moi (is_first_login = 1) thi KHONG cho phep dang nhap truc tiep
            if ($checkUser && $checkUser['is_first_login'] == 1) {
                $errors['username'] = ['Vui long dang nhap bang cach nhan vao lien ket trong email cua ban.'];
            } else {
                // Dang nhap binh thuong
                $user = Auth::login($username, $password);
                
                if ($user) {
                    // Dang nhap thanh cong
                    Session::setFlash('success', 'Chao mung ' . $user['full_name'] . '!', 'success');
                    Router::redirect(Router::url('dashboard'));
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