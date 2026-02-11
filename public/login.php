<?php
require_once __DIR__ . '/../config/bootstrap.php';

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
            Session::setFlash('success', 'Đăng nhập thành công! Vui lòng tạo mật khẩu mới.', 'success');
            Router::redirect(Router::url('first-login'));
            exit;
        } else {
            $errors['token'] = 'Liên kết đăng nhập không hợp lệ hoặc đã hết hạn. Vui lòng liên hệ quản trị viên để gửi lại email.';
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
            // Kiem tra xem co phai nhan vien moi chua dang nhap lan dau khong
            $db = Database::getInstance();
            $checkUser = $db->fetchOne(
                "SELECT is_first_login FROM users WHERE username = ?",
                [$username]
            );
            
            // Neu la nhan vien moi (is_first_login = 1) thi KHONG cho phep dang nhap truc tiep
            if ($checkUser && $checkUser['is_first_login'] == 1) {
                $errors['username'] = ['Vui lòng đăng nhập bằng cách nhấn vào liên kết trong email của bạn.'];
            } else {
                // Dang nhap binh thuong
                $user = Auth::login($username, $password);
                
                if ($user) {
                    // Dang nhap thanh cong
                    Session::setFlash('success', 'Chào mừng ' . $user['full_name'] . '!', 'success');
                    Router::redirect(Router::url('dashboard'));
                    exit;
                } else {
                    $errors['login'] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
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