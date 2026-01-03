<?php
/**
 * Database Configuration
 * File này chỉ chứa constants, KHÔNG tạo kết nối
 * 
 * Lưu ý: File này nên được .gitignore để bảo mật
 */

// Database Connection Settings
define('DB_HOST', 'localhost');           // Host của MySQL
define('DB_NAME', 'pos_system');          // Tên database vừa import
define('DB_USER', 'root');                // Username MySQL (mặc định XAMPP)
define('DB_PASS', '');                    // Password MySQL (mặc định XAMPP để trống)
define('DB_CHARSET', 'utf8mb4');          // Charset hỗ trợ tiếng Việt + emoji
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Optional: Database Engine Settings
define('DB_ENGINE', 'InnoDB');            // Storage engine
define('DB_PREFIX', '');                  // Table prefix (nếu cần)

// Optional: PDO Options (sẽ dùng trong Database class)
// Không define ở đây, sẽ define trong Database.php
?>