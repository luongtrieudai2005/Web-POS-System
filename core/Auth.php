<?php
/**
 * Authentication Class
 * Xu ly xac thuc va phan quyen
 */
class Auth {
    
    private static $db;
    
    /**
     * Khoi tao
     */
    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    /**
     * Dang nhap bang username va password
     * 
     * @return array|false Thong tin user hoac false neu that bai
     */
    public static function login($username, $password) {
        self::init();
        
        try {
            // Tim user theo username
            $user = self::$db->fetchOne(
                "SELECT * FROM users WHERE username = ? LIMIT 1",
                [$username]
            );
            
            if (!$user) {
                return false;
            }
            
            // Kiem tra account bi khoa
            if ($user['status'] === 'locked') {
                throw new Exception('Tai khoan da bi khoa. Vui long lien he quan tri vien.');
            }
            
            // Kiem tra password
            // if (!password_verify($password, $user['password'])) {
            //     return false;
            // }
            
            // Luu thong tin vao session
            Session::setUser($user);
            Session::regenerate();
            
            return $user;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Dang nhap bang token (cho nhan vien moi)
     * 
     * @return array|false
     */
    public static function loginWithToken($token) {
        self::init();
        
        try {
            // Tim user co token hop le
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
            
            // Kiem tra account bi khoa
            if ($user['status'] === 'locked') {
                throw new Exception('Tài khoản đã bị khóa.');
            }
            
            // Luu thong tin vao session
            Session::setUser($user);
            Session::regenerate();
            
            // Xoa token da su dung
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
     * Dang xuat
     */
    public static function logout() {
        Session::destroy();
    }
    
    /**
     * Kiem tra da dang nhap chua
     */
    public static function check() {
        return Session::isLoggedIn();
    }
    
    /**
     * Lay thong tin user hien tai
     */
    public static function user() {
        return Session::getUser();
    }
    
    /**
     * Lay ID user hien tai
     */
    public static function id() {
        return Session::getUserId();
    }
    
    /**
     * Kiem tra co phai admin khong
     */
    public static function isAdmin() {
        return Session::isAdmin();
    }
    
    /**
     * Kiem tra co phai salesperson khong
     */
    public static function isSalesperson() {
        return Session::isSalesperson();
    }
    
    /**
     * Yeu cau phai dang nhap
     * Neu chua dang nhap thi redirect ve trang login
     */
    public static function requireLogin() {
        if (!self::check()) {
            Session::setFlash('error', 'Vui long dang nhap de tiep tuc', 'warning');
            Router::redirect(Router::url('login.php'));
            exit;
        }
    }
    
    /**
     * Yeu cau phai la admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (!self::isAdmin()) {
            Session::setFlash('error', 'Bạn không có quyền truy cập', 'danger');
            Router::redirect(Router::url('index.php'));
            exit;
        }
    }
    
    /**
     * Yeu cau phai doi mat khau (cho first login)
     */
    public static function requirePasswordChange() {
        if (!self::check()) {
            return false;
        }
        
        $user = self::user();
        return $user['is_first_login'] == 1;
    }
    
    /**
     * Tao login token cho nhan vien moi
     * 
     * @return string Token
     */
    public static function generateLoginToken($userId) {
        self::init();
        
        // Tao token ngau nhien
        $token = bin2hex(random_bytes(32));
        
        // Tinh thoi gian het han (1 phut)
        $expiry = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_MINUTES . ' minutes'));
        
        // Luu vao database
        self::$db->execute(
            "UPDATE users 
             SET login_token = ?, token_expiry = ?
             WHERE id = ?",
            [$token, $expiry, $userId]
        );
        
        return $token;
    }
    
    /**
     * Doi mat khau
     */
    public static function changePassword($userId, $newPassword) {
        self::init();
        
        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        //$hashedPassword = $newPassword;
        
        // Cap nhat database
        $result = self::$db->execute(
            "UPDATE users 
             SET password = ?, 
                 is_first_login = 0,
                 updated_at = NOW()
             WHERE id = ?",
            [$hashedPassword, $userId]
        );
        
        // Cap nhat session
        if ($result && Session::getUserId() == $userId) {
            $user = Session::getUser();
            $user['is_first_login'] = 0;
            Session::setUser($user);
        }
        
        return $result;
    }
    
    /**
     * Kiem tra password co dung khong
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
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
?>