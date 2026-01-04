<?php
/**
 * Helper Functions
 * Cac ham tien ich dung chung
 */

class Helper {
    
    /**
     * Escape HTML de tranh XSS
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Dinh dang tien te VND
     */
    public static function formatMoney($amount) {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
    
    /**
     * Dinh dang ngay gio
     */
    public static function formatDate($datetime, $format = 'd/m/Y H:i') {
        if (empty($datetime)) {
            return '';
        }
        
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        return date($format, $timestamp);
    }
    
    /**
     * Dinh dang ngay (khong co gio)
     */
    public static function formatDateOnly($datetime) {
        return self::formatDate($datetime, 'd/m/Y');
    }
    
    /**
     * Dinh dang gio (khong co ngay)
     */
    public static function formatTimeOnly($datetime) {
        return self::formatDate($datetime, 'H:i');
    }
    
    /**
     * Tao ten file ngau nhien
     */
    public static function generateFileName($extension = '') {
        $filename = uniqid() . '_' . time();
        
        if (!empty($extension)) {
            $extension = ltrim($extension, '.');
            $filename .= '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Upload file
     * 
     * @param array $file $_FILES['fieldname']
     * @param string $uploadDir Thu muc upload (vi du: 'uploads/avatars/')
     * @param array $allowedTypes Cac dinh dang cho phep
     * @return string|false Duong dan file hoac false neu loi
     */
    public static function uploadFile($file, $uploadDir, $allowedTypes = null) {
        // Kiem tra loi upload
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Lay extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Kiem tra dinh dang
        if ($allowedTypes === null) {
            $allowedTypes = UPLOAD_ALLOWED_TYPES;
        }
        
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }
        
        // Tao thu muc neu chua ton tai
        $fullUploadDir = __DIR__ . '/../public/' . trim($uploadDir, '/') . '/';
        if (!file_exists($fullUploadDir)) {
            mkdir($fullUploadDir, 0755, true);
        }
        
        // Tao ten file moi
        $newFileName = self::generateFileName($extension);
        $destination = $fullUploadDir . $newFileName;
        
        // Di chuyen file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Tra ve duong dan tuong doi
            return trim($uploadDir, '/') . '/' . $newFileName;
        }
        
        return false;
    }
    
    /**
     * Xoa file
     */
    public static function deleteFile($filePath) {
        if (empty($filePath)) {
            return false;
        }
        
        $fullPath = __DIR__ . '/../public/' . ltrim($filePath, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Tao slug tu string (dung cho URL)
     */
    public static function slug($string) {
        // Chuyen thanh chu thuong
        $string = mb_strtolower($string, 'UTF-8');
        
        // Thay the dau cach bang -
        $string = preg_replace('/\s+/', '-', $string);
        
        // Loai bo ky tu dac biet
        $string = preg_replace('/[^a-z0-9\-]/', '', $string);
        
        // Loai bo dau - lien tiep
        $string = preg_replace('/-+/', '-', $string);
        
        // Trim dau -
        $string = trim($string, '-');
        
        return $string;
    }
    
    /**
     * Tao ma don hang ngau nhien
     */
    public static function generateOrderCode($prefix = 'ORD') {
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $date . '-' . $random;
    }
    
    /**
     * Tao ma barcode ngau nhien
     */
    public static function generateBarcode() {
        // Format: 893 (Viet Nam) + 13 chu so
        return '893' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }
    
    /**
     * Tinh toan phan trang
     */
    public static function pagination($total, $perPage = 10, $currentPage = 1) {
        $totalPages = ceil($total / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;
        
        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'prev_page' => $currentPage - 1,
            'next_page' => $currentPage + 1
        ];
    }
    
    /**
     * Truncate string (cat chuoi)
     */
    public static function truncate($string, $length = 100, $suffix = '...') {
        if (mb_strlen($string, 'UTF-8') <= $length) {
            return $string;
        }
        
        return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
    }
    
    /**
     * Debug function (chi hien thi khi APP_DEBUG = true)
     */
    public static function dd($data) {
        if (!APP_DEBUG) {
            return;
        }
        
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
    
    /**
     * Redirect voi message
     */
    public static function redirectWithMessage($url, $message, $type = 'success') {
        Session::setFlash('message', $message, $type);
        Router::redirect($url);
    }
    
    /**
     * Kiem tra request la AJAX khong
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
               && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Tra ve JSON response
     */
    public static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Sanitize input (lam sach du lieu)
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Kiem tra co phai POST request khong
     */
    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Kiem tra co phai GET request khong
     */
    public static function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Lay gia tri tu $_POST
     */
    public static function post($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Lay gia tri tu $_GET
     */
    public static function get($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Tao CSRF token
     */
    public static function generateCsrfToken() {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
    
    /**
     * Kiem tra CSRF token
     */
    public static function verifyCsrfToken($token) {
        return Session::get('csrf_token') === $token;
    }
    
    /**
     * Render view
     */
    public static function view($viewPath, $data = []) {
        // Extract data thanh bien
        extract($data);
        
        // Include view file
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: $viewPath");
        }
        
        include $viewFile;
    }
    
    /**
     * Tinh thoi gian tuong doi (1 gio truoc, 2 ngay truoc...)
     */
    public static function timeAgo($datetime) {
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return $diff . ' giay truoc';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phut truoc';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' gio truoc';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . ' ngay truoc';
        } else {
            return self::formatDate($datetime);
        }
    }
}
?>