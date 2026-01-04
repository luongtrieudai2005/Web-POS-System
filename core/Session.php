<?php
/**
 * Session Management Class
 * Quan ly session an toan, chong tan cong session fixation/hijacking
 */
class Session {
    
    /**
     * Khoi tao session neu chua co
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Cau hinh session an toan
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Dat = 1 neu dung HTTPS
            
            session_start();
            
            // Thiet lap thoi gian het han
            if (!self::has('last_activity')) {
                self::set('last_activity', time());
            }
            
            // Kiem tra timeout (1 gio)
            if (time() - self::get('last_activity') > SESSION_LIFETIME) {
                self::destroy();
                return false;
            }
            
            self::set('last_activity', time());
        }
        return true;
    }
    
    /**
     * Set gia tri session
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Lay gia tri session
     */
    public static function get($key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Kiem tra key co ton tai khong
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Xoa mot key
     */
    public static function delete($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Huy toan bo session
     */
    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
        
        // Xoa cookie session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Tao lai session ID (phong tan cong session fixation)
     */
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }
    
    /**
     * Flash message - hien thi 1 lan roi xoa
     */
    public static function setFlash($key, $message, $type = 'info') {
        self::set('flash_' . $key, [
            'message' => $message,
            'type' => $type
        ]);
    }
    
    /**
     * Lay flash message va xoa
     */
    public static function getFlash($key) {
        $flashKey = 'flash_' . $key;
        $flash = self::get($flashKey);
        self::delete($flashKey);
        return $flash;
    }
    
    /**
     * Kiem tra co flash message khong
     */
    public static function hasFlash($key) {
        return self::has('flash_' . $key);
    }
    
    /**
     * Luu thong tin user dang dang nhap
     */
    public static function setUser($user) {
        self::set('user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'avatar' => $user['avatar'],
            'is_first_login' => $user['is_first_login']
        ]);
    }
    
    /**
     * Lay thong tin user dang dang nhap
     */
    public static function getUser() {
        return self::get('user');
    }
    
    /**
     * Lay ID user dang dang nhap
     */
    public static function getUserId() {
        $user = self::getUser();
        return $user ? $user['id'] : null;
    }
    
    /**
     * Kiem tra user da dang nhap chua
     */
    public static function isLoggedIn() {
        return self::has('user');
    }
    
    /**
     * Lay role cua user
     */
    public static function getUserRole() {
        $user = self::getUser();
        return $user ? $user['role'] : null;
    }
    
    /**
     * Kiem tra co phai admin khong
     */
    public static function isAdmin() {
        return self::getUserRole() === 'admin';
    }
    
    /**
     * Kiem tra co phai salesperson khong
     */
    public static function isSalesperson() {
        return self::getUserRole() === 'salesperson';
    }
    
    /**
     * Kiem tra co phai first login khong
     */
    public static function isFirstLogin() {
        $user = self::getUser();
        return $user && $user['is_first_login'];
    }
}
?>