<?php
/**
 * Lớp Xác thực
 * Xử lý đăng nhập, đăng xuất và phân quyền người dùng
 */

class Auth {
    
    private static $db;
    
    /**
     * Khởi tạo kết nối database
     */
    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    /**
     * Đăng nhập bằng tên đăng nhập và mật khẩu
     * 
     * @return array|false Thông tin người dùng hoặc false nếu thất bại
     */
    public static function login($username, $password) {
        self::init();
        
        // Tìm người dùng theo tên đăng nhập
        $user = self::$db->fetchOne(
            "SELECT * FROM users WHERE username = ? LIMIT 1",
            [$username]
        );
        
        if (!$user) {
            return false;
        }
        
        // Kiểm tra tài khoản bị khóa
        if ($user['status'] === 'locked') {
            throw new Exception('Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }
        
        // Kiểm tra mật khẩu
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        // Lưu thông tin vào session
        Session::setUser($user);
        Session::regenerate();
        
        return $user;
    }
    
    /**
     * Đăng nhập bằng token (dành cho nhân viên mới lần đầu)
     * 
     * @return array|false
     */
    public static function loginWithToken($token) {
        self::init();
        
        try {
            // Tìm người dùng có token hợp lệ
            $user = self::$db->fetchOne(
                "SELECT * FROM users 
                 WHERE login_token = ? 
                 AND token_expiry > NOW()
                 AND is_first_login = 1
                 LIMIT 1",
                [$token]
            );
            
            if (!$user) {
                return false;
            }
            
            // Kiểm tra tài khoản bị khóa
            if ($user['status'] === 'locked') {
                throw new Exception('Tài khoản đã bị khóa.');
            }
            
            // Lưu thông tin vào session
            Session::setUser($user);
            Session::regenerate();
            
            // Xóa token đã sử dụng
            self::$db->execute(
                "UPDATE users SET login_token = NULL, token_expiry = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return $user;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Đăng xuất
     */
    public static function logout() {
        Session::destroy();
    }
    
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    public static function check() {
        return Session::isLoggedIn();
    }
    
    /**
     * Lấy thông tin người dùng hiện tại
     */
    public static function user() {
        return Session::getUser();
    }
    
    /**
     * Lấy ID người dùng hiện tại
     */
    public static function id() {
        return Session::getUserId();
    }
    
    /**
     * Kiểm tra người dùng có phải admin không
     */
    public static function isAdmin() {
        return Session::isAdmin();
    }
    
    /**
     * Kiểm tra người dùng có phải nhân viên bán hàng không
     */
    public static function isSalesperson() {
        return Session::isSalesperson();
    }
    
    /**
     * Yêu cầu phải đăng nhập
     * Nếu chưa đăng nhập thì chuyển hướng về trang đăng nhập
     */
    public static function requireLogin() {
        if (!self::check()) {
            Session::setFlash('error', 'Vui lòng đăng nhập để tiếp tục', 'warning');
            Router::redirect(Router::url('login'));
            exit;
        }
    }
    
    /**
     * Yêu cầu phải là admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (!self::isAdmin()) {
            Session::setFlash('error', 'Bạn không có quyền truy cập trang này', 'danger');
            Router::redirect(Router::url('index.php'));
            exit;
        }
    }
    
    /**
     * Yêu cầu đổi mật khẩu (dành cho lần đăng nhập đầu tiên)
     */
    public static function requirePasswordChange() {
        if (!self::check()) {
            return false;
        }
        
        $user = self::user();
        return $user['is_first_login'] == 1;
    }
    
    /**
     * Tạo token đăng nhập cho nhân viên mới
     * 
     * @return string Token
     */
    public static function generateLoginToken($userId) {
        self::init();
        
        // Tạo token ngẫu nhiên
        $token = bin2hex(random_bytes(32));
        
        // Tính thời gian hết hạn
        $expiry = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_MINUTES . ' minutes'));
        
        // Lưu vào cơ sở dữ liệu
        self::$db->execute(
            "UPDATE users 
             SET login_token = ?, token_expiry = ?
             WHERE id = ?",
            [$token, $expiry, $userId]
        );
        
        return $token;
    }
    
    /**
     * Đổi mật khẩu
     */
    public static function changePassword($userId, $newPassword) {
        self::init();
        
        // Mã hóa mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật cơ sở dữ liệu
        $result = self::$db->execute(
            "UPDATE users 
             SET password = ?, 
                 is_first_login = 0,
                 updated_at = NOW()
             WHERE id = ?",
            [$hashedPassword, $userId]
        );
        
        // Cập nhật session nếu người dùng đang đăng nhập
        if ($result && Session::getUserId() == $userId) {
            $user = Session::getUser();
            $user['is_first_login'] = 0;
            Session::setUser($user);
        }
        
        return $result;
    }
    
    /**
     * Kiểm tra mật khẩu có đúng không
     */
    public static function verifyPassword($userId, $password) {
        self::init();
        
        $user = self::$db->fetchOne(
            "SELECT password FROM users WHERE id = ?",
            [$userId]
        );
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
    
    /**
     * Mã hóa mật khẩu
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}