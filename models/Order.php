<?php
require_once __DIR__ . '/../config/bootstrap.php';

class Order {

    private static $db;

    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }

    public static function create($data) {
        self::init();
        $db = self::$db;
        $db->beginTransaction();
        try {
            $orderCode = Helper::generateOrderCode();

            $totalProfit = 0;
            foreach ($data['items'] as $item) {
                $totalProfit += ($item['unit_price'] - $item['import_price']) * $item['quantity'];
            }

            $db->execute(
                "INSERT INTO orders (order_code, customer_id, employee_id, total_amount, amount_paid,
                 change_amount, total_items, total_profit, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $orderCode,
                    $data['customer_id'],
                    Auth::id(),
                    $data['total_amount'],
                    $data['amount_paid'],
                    $data['change_amount'],
                    $data['total_items'],
                    $totalProfit
                ]
            );

            $orderId = $db->lastInsertId();

            foreach ($data['items'] as $item) {
                $subtotal = $item['unit_price'] * $item['quantity'];
                $profit = ($item['unit_price'] - $item['import_price']) * $item['quantity'];

                $db->execute(
                    "INSERT INTO order_details (order_id, product_id, quantity, unit_price, import_price, subtotal, profit)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $orderId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['unit_price'],
                        $item['import_price'],
                        $subtotal,
                        $profit
                    ]
                );

                // Giam ton kho
                $db->execute(
                    "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }

            $db->commit();
            return $orderId;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function getById($id) {
        self::init();
        return self::$db->fetchOne(
            "SELECT o.*, c.full_name as customer_name, c.phone as customer_phone,
                    c.address as customer_address, u.full_name as employee_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             LEFT JOIN users u ON o.employee_id = u.id
             WHERE o.id = ?",
            [$id]
        );
    }

    public static function getDetails($orderId) {
        self::init();
        return self::$db->fetchAll(
            "SELECT od.*, p.name as product_name, p.barcode
             FROM order_details od
             LEFT JOIN products p ON od.product_id = p.id
             WHERE od.order_id = ?",
            [$orderId]
        );
    }

    // Bao cao theo khoang thoi gian
    public static function getReport($dateFrom, $dateTo, $employeeId = null) {
        self::init();
        $params = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];
        $where = "WHERE o.created_at BETWEEN ? AND ?";
        if ($employeeId) {
            $where .= " AND o.employee_id = ?";
            $params[] = $employeeId;
        }

        $summary = self::$db->fetchOne(
            "SELECT COUNT(o.id) as total_orders,
                    IFNULL(SUM(o.total_amount), 0) as total_revenue,
                    IFNULL(SUM(o.total_profit), 0) as total_profit,
                    IFNULL(SUM(o.total_items), 0) as total_items
             FROM orders o $where",
            $params
        );

        $orders = self::$db->fetchAll(
            "SELECT o.*, c.full_name as customer_name, c.phone as customer_phone,
                    u.full_name as employee_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             LEFT JOIN users u ON o.employee_id = u.id
             $where
             ORDER BY o.created_at DESC",
            $params
        );

        return ['summary' => $summary, 'orders' => $orders];
    }

    // Doanh thu theo ngay (cho bieu do)
    // public static function getRevenueByDay($dateFrom, $dateTo) {
    //     self::init();
    //     return self::$db->fetchAll(
    //         "SELECT DATE(created_at) as date,
    //                 SUM(total_amount) as revenue,
    //                 COUNT(id) as orders
    //          FROM orders
    //          WHERE created_at BETWEEN ? AND ?
    //          GROUP BY DATE(created_at)
    //          ORDER BY date ASC",
    //         [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']
    //     );
    // }
    public static function getRevenueByDay($dateFrom, $dateTo, $employeeId = null) {
            self::init();
            $where  = "WHERE created_at BETWEEN ? AND ?";
            $params = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];

            if ($employeeId) {
                $where   .= " AND employee_id = ?";
                $params[] = $employeeId;
            }

            return self::$db->fetchAll(
                "SELECT DATE(created_at) as date,
                        SUM(total_amount) as revenue,
                        COUNT(id) as orders
                 FROM orders
                 $where
                 GROUP BY DATE(created_at)
                 ORDER BY date ASC",
                $params
            );
        }

    
    // Top san pham ban chay
    public static function getTopProducts($dateFrom, $dateTo, $limit = 5) {
        self::init();
        return self::$db->fetchAll(
            "SELECT p.name, p.barcode, SUM(od.quantity) as total_qty,
                    SUM(od.subtotal) as total_revenue
             FROM order_details od
             JOIN products p ON od.product_id = p.id
             JOIN orders o ON od.order_id = o.id
             WHERE o.created_at BETWEEN ? AND ?
             GROUP BY p.id
             ORDER BY total_qty DESC
             LIMIT ?",
            [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59', $limit]
        );
    }
}
