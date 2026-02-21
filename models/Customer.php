<?php
require_once __DIR__ . '/../config/bootstrap.php';

class Customer {

    private static $db;

    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }

    public static function getByPhone($phone) {
        self::init();
        return self::$db->fetchOne(
            "SELECT * FROM customers WHERE phone = ?",
            [$phone]
        );
    }

    public static function getById($id) {
        self::init();
        return self::$db->fetchOne(
            "SELECT * FROM customers WHERE id = ?",
            [$id]
        );
    }

    public static function create($data) {
        self::init();
        $result = self::$db->execute(
            "INSERT INTO customers (phone, full_name, address, created_at) VALUES (?, ?, ?, NOW())",
            [$data['phone'], $data['full_name'], $data['address'] ?? null]
        );
        if ($result) {
            return self::$db->lastInsertId();
        }
        return false;
    }

    public static function getOrCreate($phone, $fullName, $address = null) {
        self::init();
        $customer = self::getByPhone($phone);
        if ($customer) {
            return $customer;
        }
        $id = self::create([
            'phone' => $phone,
            'full_name' => $fullName,
            'address' => $address
        ]);
        return self::getById($id);
    }

    // Lấy lịch sử mua hàng của khách
    public static function getOrderHistory($customerId) {
        self::init();
        return self::$db->fetchAll(
            "SELECT o.*, u.full_name as employee_name,
                    (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count
             FROM orders o
             LEFT JOIN users u ON o.employee_id = u.id
             WHERE o.customer_id = ?
             ORDER BY o.created_at DESC",
            [$customerId]
        );
    }


    // Lấy hết luôn
    public static function getAll($filters = []) {
        self::init();
        $sql = "SELECT c.*,
                    COUNT(o.id) as total_orders,
                    IFNULL(SUM(o.total_amount), 0) as total_spent
                FROM customers c
                LEFT JOIN orders o ON c.id = o.customer_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['search'])) {
            $sql .= " AND (c.phone LIKE ? OR c.full_name LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }
        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";
        return self::$db->fetchAll($sql, $params);
    }
}
