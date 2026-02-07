<?php
/**
 * First Login Page
 * Trang bat buoc doi mat khau cho nhan vien moi
 */

// Load bootstrap
require_once __DIR__ . '/../config/bootstrap.php';

// Kiem tra da dang nhap chua
Auth::requireLogin();

// Neu khong phai first login thi redirect ve dashboard
if (!Auth::requirePasswordChange()) {
    Router::redirect(Router::url('dashboard.php'));
    exit;
}

$errors = [];

// XU LY DOI MAT KHAU
if (Helper::isPost()) {
    $newPassword = Helper::post('new_password', '');
    $confirmPassword = Helper::post('confirm_password', '');
    
    // Validation
    $validator = new Validator($_POST);
    $validator->validate([
        'new_password' => 'required|min:' . PASSWORD_MIN_LENGTH,
        'confirm_password' => 'required|match:new_password'
    ]);
    
    if ($validator->fails()) {
        $errors = $validator->errors();
    } else {
        try {
            // Doi mat khau
            $userId = Auth::id();
            $result = Auth::changePassword($userId, $newPassword);
            
            if ($result) {
                // Thanh cong
                Session::setFlash('success', 'Đổi mật khẩu thành công! Bạn có thể sử dụng hệ thống bình thường.', 'success');
                Router::redirect(Router::url('dashboard.php'));
                exit;
            } else {
                $errors['general'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        } catch (Exception $e) {
            $errors['general'] = $e->getMessage();
        }
    }
}

// Lay thong tin user
$user = Auth::user();

// Hien thi view
require_once __DIR__ . '/../views/auth/first-login.php';
?>