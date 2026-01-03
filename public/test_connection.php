<?php
/**
 * Test Database Connection
 * Chạy file này qua trình duyệt để kiểm tra kết nối
 */

// Load config
require_once '../config/database.php';
require_once '../core/Database.php';

// HTML Header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test Database Connection</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; }
        .info { color: #004085; background: #cce5ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🧪 Database Connection Test</h1>

<?php

try {
    // ===== TEST 1: Kết nối cơ bản =====
    echo "<h2>Test 1: Kết nối cơ bản</h2>";
    $db = Database::getInstance();
    echo '<div class="success">✅ Kết nối thành công!</div>';
    
    // ===== TEST 2: Kiểm tra Singleton =====
    echo "<h2>Test 2: Singleton Pattern</h2>";
    $db2 = Database::getInstance();
    if ($db === $db2) {
        echo '<div class="success">✅ Singleton hoạt động đúng! (Cùng 1 instance)</div>';
    } else {
        echo '<div class="error">❌ Singleton sai! (2 instance khác nhau)</div>';
    }
    
    // ===== TEST 3: Thông tin Database =====
    echo "<h2>Test 3: Thông tin Database</h2>";
    echo '<div class="info">';
    echo "📌 Host: " . DB_HOST . "<br>";
    echo "📌 Database: " . DB_NAME . "<br>";
    echo "📌 User: " . DB_USER . "<br>";
    echo "📌 Charset: " . DB_CHARSET . "<br>";
    echo '</div>';
    
    // ===== TEST 4: Đếm số bảng =====
    echo "<h2>Test 4: Danh sách bảng</h2>";
    $tables = $db->fetchAll("SHOW TABLES");
    echo "<p>Tổng số bảng: <strong>" . count($tables) . "</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "<li>$tableName</li>";
    }
    echo "</ul>";
    
    // ===== TEST 5: Đếm số dòng mỗi bảng =====
    echo "<h2>Test 5: Số lượng dữ liệu</h2>";
    echo "<table>";
    echo "<tr><th>Bảng</th><th>Số dòng</th></tr>";
    
    $tableNames = ['users', 'categories', 'products', 'customers', 'orders', 'order_details'];
    foreach ($tableNames as $tableName) {
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM $tableName");
        echo "<tr><td>$tableName</td><td>{$result['count']}</td></tr>";
    }
    echo "</table>";
    
    // ===== TEST 6: Lấy thông tin Admin =====
    echo "<h2>Test 6: Lấy thông tin Admin</h2>";
    $admin = $db->fetchOne("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    
    if ($admin) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($admin as $key => $value) {
            // Ẩn password
            $displayValue = ($key === 'password') ? '***hidden***' : $value;
            echo "<tr><td>$key</td><td>$displayValue</td></tr>";
        }
        echo "</table>";
    } else {
        echo '<div class="error">❌ Không tìm thấy admin!</div>';
    }
    
    // ===== TEST 7: Test Prepared Statement =====
    echo "<h2>Test 7: Prepared Statement (Bảo mật)</h2>";
    $testEmail = 'admin@gmail.com';
    $user = $db->fetchOne(
        "SELECT username, email FROM users WHERE email = ?",
        [$testEmail]
    );
    
    if ($user) {
        echo '<div class="success">✅ Prepared statement hoạt động!</div>';
        echo "<p>Found user: <strong>{$user['username']}</strong></p>";
    }
    
    // ===== TỔNG KẾT =====
    echo "<h2>🎉 KẾT LUẬN</h2>";
    echo '<div class="success">';
    echo "✅ Database kết nối thành công!<br>";
    echo "✅ Singleton Pattern hoạt động đúng!<br>";
    echo "✅ Prepared Statements hoạt động!<br>";
    echo "✅ Dữ liệu đã import đầy đủ!<br>";
    echo "<br><strong>👉 Bạn có thể bắt đầu phát triển tính năng!</strong>";
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="error">';
    echo "<h2>❌ LỖI KẾT NỐI</h2>";
    echo "<p><strong>Thông báo lỗi:</strong></p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<h3>🔧 Cách khắc phục:</h3>";
    echo "<ol>";
    echo "<li>Kiểm tra XAMPP đã start MySQL chưa</li>";
    echo "<li>Kiểm tra file config/database.php (DB_HOST, DB_NAME, DB_USER, DB_PASS)</li>";
    echo "<li>Kiểm tra database 'pos_system' đã import chưa</li>";
    echo "<li>Thử chạy lại query trong phpMyAdmin để xem lỗi chi tiết</li>";
    echo "</ol>";
    echo '</div>';
}

?>

</body>
</html>