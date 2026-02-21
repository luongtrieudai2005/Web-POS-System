<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Mailer.php';

class UserController {
    
    public static function index() {
        Auth::requireAdmin();
        
        $search = Helper::get('search', '');
        $role = Helper::get('role', '');
        $status = Helper::get('status', '');
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        if ($role) $filters['role'] = $role;
        if ($status) $filters['status'] = $status;
        
        $users = User::getAll($filters);
        
        require_once __DIR__ . '/../views/users/index.php';
    }
    
    public static function create() {
        Auth::requireAdmin();
        
        $errors = [];
        $formData = [];
        
        if (Helper::isPost()) {
            $formData = [
                'full_name' => Helper::post('full_name', ''),
                'email' => Helper::post('email', ''),
                'phone' => Helper::post('phone', ''),
                'role' => Helper::post('role', 'salesperson')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'full_name' => 'required|min:3|max:100',
                'email' => 'required|email',
                'role' => 'required|in:admin,salesperson'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (User::emailExists($formData['email'])) {
                    $errors['email'] = ['Email da ton tai trong he thong'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $hashedPassword = password_hash(PASSWORD_TEMP, PASSWORD_DEFAULT);
                    
                    $userId = User::create([
                        'full_name' => $formData['full_name'],
                        'email' => $formData['email'],
                        'phone' => $formData['phone'],
                        'password' => $hashedPassword,
                        'role' => $formData['role']
                    ]);
                    
                    if ($userId) {
                        $token = Auth::generateLoginToken($userId);
                        
                        try {
                            $mailer = new Mailer();
                            $emailSent = $mailer->sendEmployeeRegistration(
                                $formData['email'],
                                $formData['full_name'],
                                $token
                            );
                            
                            if ($emailSent) {
                                Session::setFlash('success', 'Tao nhan vien thanh cong! Email da duoc gui den ' . $formData['email'], 'success');
                            } else {
                                Session::setFlash('success', 'Tao nhan vien thanh cong! Nhung khong gui duoc email. Vui long kiem tra cau hinh SMTP.', 'warning');
                            }
                        } catch (Exception $e) {
                            Session::setFlash('success', 'Tao nhan vien thanh cong! Loi gui email: ' . $e->getMessage(), 'warning');
                        }
                        
                        Router::redirect(Router::url('users/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi tao nhan vien';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/users/create.php';
    }
    
    public static function detail($id) {
        Auth::requireAdmin();
        
        $user = User::getById($id);
        
        if (!$user) {
            Session::setFlash('error', 'Khong tim thay nhan vien', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/users/detail.php';
    }
    
    public static function edit($id) {
        Auth::requireAdmin();
        
        $user = User::getById($id);
        
        if (!$user) {
            Session::setFlash('error', 'Khong tim thay nhan vien', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        if ($user['id'] == 1 && Auth::id() != 1) {
            Session::setFlash('error', 'Ban khong the chinh sua tai khoan admin chinh', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        $errors = [];
        
        if (Helper::isPost()) {
            $formData = [
                'full_name' => Helper::post('full_name', ''),
                'email' => Helper::post('email', ''),
                'phone' => Helper::post('phone', ''),
                'role' => Helper::post('role', ''),
                'status' => Helper::post('status', '')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'full_name' => 'required|min:3|max:100',
                'email' => 'required|email',
                'role' => 'required|in:admin,salesperson',
                'status' => 'required|in:active,inactive,locked'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (User::emailExists($formData['email'], $id)) {
                    $errors['email'] = ['Email da ton tai trong he thong'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $result = User::update($id, $formData);
                    
                    if ($result) {
                        Session::setFlash('success', 'Cap nhat thong tin thanh cong', 'success');
                        Router::redirect(Router::url('users/detail.php?id=' . $id));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi cap nhat';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/users/edit.php';
    }
    
    public static function resendEmail($id) {
        Auth::requireAdmin();
        
        $user = User::getById($id);
        
        if (!$user) {
            Session::setFlash('error', 'Khong tim thay nhan vien', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        if ($user['is_first_login'] != 1) {
            Session::setFlash('error', 'Nhan vien da dang nhap roi', 'warning');
            Router::redirect(Router::url('users/detail.php?id=' . $id));
            exit;
        }
        
        try {
            $token = Auth::generateLoginToken($id);
            
            $mailer = new Mailer();
            $emailSent = $mailer->sendEmployeeRegistration(
                $user['email'],
                $user['full_name'],
                $token
            );
            
            if ($emailSent) {
                Session::setFlash('success', 'Email da duoc gui lai den ' . $user['email'], 'success');
            } else {
                Session::setFlash('error', 'Khong the gui email. Vui long kiem tra cau hinh SMTP.', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', 'Loi: ' . $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('users/detail.php?id=' . $id));
        exit;
    }
    
    public static function changeStatus($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        $user = User::getById($id);
        
        if (!$user) {
            Session::setFlash('error', 'Không tìm thấy nhân viên', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        if ($user['id'] == 1) {
            Session::setFlash('error', 'Khong the thay doi trang thai admin chinh', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        $status = Helper::post('status', '');
        
        if (!in_array($status, ['active', 'inactive', 'locked'])) {
            Session::setFlash('error', 'Trạng thái không hợp lệ', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        try {
            $result = User::changeStatus($id, $status);
            
            if ($result) {
                Session::setFlash('success', 'Thay doi trang thai thanh cong', 'success');
            } else {
                Session::setFlash('error', 'Có lỗi xảy ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('users/index.php'));
        exit;
    }
    
    public static function delete($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        $user = User::getById($id);
        
        if (!$user) {
            Session::setFlash('error', 'Không tìm thấy nhân viên', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        if ($user['id'] == 1) {
            Session::setFlash('error', 'Không thể xóa tài khoản admin chính', 'danger');
            Router::redirect(Router::url('users/index.php'));
            exit;
        }
        
        try {
            $result = User::delete($id);
            
            if ($result) {
                Session::setFlash('success', 'Xóa nhân viên thành công', 'success');
            } else {
                Session::setFlash('error', 'Có lỗi xảy ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('users/index.php'));
        exit;
    }
}