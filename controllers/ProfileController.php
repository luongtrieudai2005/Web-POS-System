<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {

    public static function index() {
        Auth::requireLogin();
        if (Auth::requirePasswordChange()) {
            Router::redirect(Router::url('first-login'));
            exit;
        }

        $errors = [];
        $success = '';
        $currentUser = User::getById(Auth::id());

        if (Helper::isPost()) {
            $action = Helper::post('action', '');

            if ($action === 'update_info') {
                $fullName = trim(Helper::post('full_name', ''));
                $phone = trim(Helper::post('phone', ''));
                $address = trim(Helper::post('address', ''));

                $validator = new Validator(['full_name' => $fullName]);
                $validator->validate(['full_name' => 'required|min:3|max:100']);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                } else {
                    // Upload avatar nếu có
                    $avatarPath = null;
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                        $avatarPath = Helper::uploadFile($_FILES['avatar'], 'uploads/avatars', ['jpg', 'jpeg', 'png', 'gif']);
                        if (!$avatarPath) {
                            $errors['avatar'] = ['Tệp ảnh không hợp lệ (chỉ chấp nhận jpg, jpeg, png, gif)'];
                        }
                    }

                    if (empty($errors)) {
                        $updateData = [
                            'full_name' => $fullName,
                            'phone' => $phone,
                            'address' => $address
                        ];
                        if ($avatarPath) {
                            // Xóa ảnh cũ
                            if ($currentUser['avatar']) {
                                Helper::deleteFile($currentUser['avatar']);
                            }
                            $updateData['avatar'] = $avatarPath;
                        }
                        User::updateProfile(Auth::id(), $updateData);

                        // Cập nhật session
                        $updatedUser = User::getById(Auth::id());
                        Session::setUser($updatedUser);
                        $currentUser = $updatedUser;
                        Session::setFlash('success', 'Cập nhật thông tin thành công', 'success');
                        Router::redirect(Router::url('profile/index.php'));
                        exit;
                    }
                }
            } elseif ($action === 'change_password') {
                $currentPassword = Helper::post('current_password', '');
                $newPassword = Helper::post('new_password', '');
                $confirmPassword = Helper::post('confirm_password', '');

                if (!Auth::verifyPassword(Auth::id(), $currentPassword)) {
                    $errors['current_password'] = ['Mật khẩu hiện tại không đúng'];
                } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                    $errors['new_password'] = ['Mật khẩu mới phải có ít nhất ' . PASSWORD_MIN_LENGTH . ' ký tự'];
                } elseif ($newPassword !== $confirmPassword) {
                    $errors['confirm_password'] = ['Xác nhận mật khẩu không khớp'];
                } else {
                    Auth::changePassword(Auth::id(), $newPassword);
                    Session::setFlash('success', 'Đổi mật khẩu thành công', 'success');
                    Router::redirect(Router::url('profile/index.php'));
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../views/profile/index.php';
    }
}