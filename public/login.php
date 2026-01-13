<?php
/**
 * Login Page - WITH DEBUG
 */

// Neu da dang nhap roi thi redirect ve dashboard
if (Auth::check()) {
    Router::redirect(Router::url('dashboard'));
    exit;
}

$errors = [];
$username = '';
$token = Helper::get('token', '');
$debug = []; // THÊM DEBUG ARRAY

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
    
    // DEBUG: Log input
    $debug[] = "Username nhập: '$username'";
    $debug[] = "Password nhập: '$password'";
    
    // Validation
    $validator = new Validator($_POST);
    $validator->validate([
        'username' => 'required|min:3|max:50',
        'password' => 'required|min:3'
    ]);
    
    if ($validator->fails()) {
        $errors = $validator->errors();
        $debug[] = "Validation FAILED: " . json_encode($errors);
    } else {
        $debug[] = "Validation PASSED";
        
        try {
            // DEBUG: Check user in DB
            $db = Database::getInstance();
            $checkUser = $db->fetchOne(
                "SELECT * FROM users WHERE username = ?",
                [$username]
            );
            
            if (!$checkUser) {
                $debug[] = "❌ User KHÔNG tồn tại trong DB";
            } else {
                $debug[] = "✅ User TÌM THẤY trong DB";
                $debug[] = "is_first_login: " . $checkUser['is_first_login'];
                $debug[] = "status: " . $checkUser['status'];
                $debug[] = "Password hash: " . substr($checkUser['password'], 0, 30) . "...";
                
                // Test password verify
                $isPasswordCorrect = password_verify($password, $checkUser['password']);
                $debug[] = "password_verify() result: " . ($isPasswordCorrect ? "TRUE" : "FALSE");
            }
            
            // Neu la nhan vien moi (is_first_login = 1) thi KHONG cho phep dang nhap truc tiep
            if ($checkUser && $checkUser['is_first_login'] == 1) {
                $debug[] = "❌ REJECT: Nhân viên mới phải dùng link email";
                $errors['username'] = ['Vui long dang nhap bang cach nhan vao lien ket trong email cua ban.'];
            } else {
                // Dang nhap binh thuong
                $debug[] = "Gọi Auth::login()...";
                $user = Auth::login($username, $password);
                
                if ($user) {
                    $debug[] = "✅ LOGIN THÀNH CÔNG!";
                    // Dang nhap thanh cong
                    Session::setFlash('success', 'Chao mung ' . $user['full_name'] . '!', 'success');
                    Router::redirect(Router::url('dashboard'));
                    exit;
                } else {
                    $debug[] = "❌ Auth::login() returned FALSE";
                    $errors['login'] = 'Ten dang nhap hoac mat khau khong chinh xac.';
                }
            }
        } catch (Exception $e) {
            $debug[] = "❌ EXCEPTION: " . $e->getMessage();
            $errors['login'] = $e->getMessage();
        }
    }
}

// Hien thi view
require_once __DIR__ . '/../views/auth/login.php';
?>