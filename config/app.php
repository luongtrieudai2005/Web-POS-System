<?php
/**
 * Application Configuration
 * Các cấu hình chung của ứng dụng
 */

// Application Settings
define('APP_NAME', 'POS System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/project-root/source/public');


// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error Reporting (Development mode)
define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Email Settings (cho tính năng gửi email)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'luongtrieudai0902@gmail.com');
define('MAIL_PASSWORD', 'lfxw wsox btdi umag');
define('MAIL_FROM_EMAIL', 'noreply@possystem.com');
define('MAIL_FROM_NAME', 'POS System');

// Password Settings
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_TEMP', '52300185'); // MSSV trưởng nhóm

// Token Settings
define('TOKEN_EXPIRY_MINUTES', 1); // Email link có hiệu lực 1 phút
?>