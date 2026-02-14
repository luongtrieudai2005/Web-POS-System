<?php
require_once __DIR__ . '/../config/bootstrap.php';

class Product {
    
    private static $db;
    
    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    /**
     * Lay tat ca san pham voi filters
     */
    public static function getAll($filters = []) {
        self::init();
        
        $sql = "SELECT p.*, c.name as category_name, u.full_name as creator_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.created_by = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND (p.barcode LIKE ? OR p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (isset($filters['barcode']) && !empty($filters['barcode'])) {
            $sql .= " AND p.barcode = ?";
            $params[] = $filters['barcode'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return self::$db->fetchAll($sql, $params);
    }
    
    /**
     * Lay san pham theo ID
     */
    public static function getById($id) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT p.*, c.name as category_name, u.full_name as creator_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN users u ON p.created_by = u.id
             WHERE p.id = ?",
            [$id]
        );
    }
    
    /**
     * Lay san pham theo barcode
     */
    public static function getByBarcode($barcode) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT p.*, c.name as category_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.barcode = ?",
            [$barcode]
        );
    }
    
    /**
     * Tao san pham moi
     */
    public static function create($data) {
        self::init();
        
        $sql = "INSERT INTO products (
                    barcode, name, category_id, import_price, retail_price, 
                    stock_quantity, image, description, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['barcode'],
            $data['name'],
            $data['category_id'] ?? null,
            $data['import_price'],
            $data['retail_price'],
            $data['stock_quantity'] ?? 0,
            $data['image'] ?? null,
            $data['description'] ?? null,
            Auth::id()
        ];
        
        $result = self::$db->execute($sql, $params);
        
        if ($result) {
            return self::$db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cap nhat san pham
     */
    public static function update($id, $data) {
        self::init();
        
        $sql = "UPDATE products SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['barcode'])) {
            $updates[] = "barcode = ?";
            $params[] = $data['barcode'];
        }
        
        if (isset($data['name'])) {
            $updates[] = "name = ?";
            $params[] = $data['name'];
        }
        
        if (isset($data['category_id'])) {
            $updates[] = "category_id = ?";
            $params[] = $data['category_id'];
        }
        
        if (isset($data['import_price'])) {
            $updates[] = "import_price = ?";
            $params[] = $data['import_price'];
        }
        
        if (isset($data['retail_price'])) {
            $updates[] = "retail_price = ?";
            $params[] = $data['retail_price'];
        }
        
        if (isset($data['stock_quantity'])) {
            $updates[] = "stock_quantity = ?";
            $params[] = $data['stock_quantity'];
        }
        
        if (isset($data['image'])) {
            $updates[] = "image = ?";
            $params[] = $data['image'];
        }
        
        if (isset($data['description'])) {
            $updates[] = "description = ?";
            $params[] = $data['description'];
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $updates[] = "updated_at = NOW()";
        
        $sql .= implode(', ', $updates);
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return self::$db->execute($sql, $params);
    }
    
    /**
     * Xoa san pham
     */
    public static function delete($id) {
        self::init();
        
        $orderDetailCount = self::$db->fetchOne(
            "SELECT COUNT(*) as count FROM order_details WHERE product_id = ?",
            [$id]
        );
        
        if ($orderDetailCount['count'] > 0) {
            throw new Exception('Khong the xoa san pham da co trong don hang');
        }
        
        return self::$db->execute(
            "DELETE FROM products WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Kiem tra barcode da ton tai chua
     */
    public static function barcodeExists($barcode, $excludeId = null) {
        self::init();
        
        if ($excludeId) {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM products WHERE barcode = ? AND id != ?",
                [$barcode, $excludeId]
            );
        } else {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM products WHERE barcode = ?",
                [$barcode]
            );
        }
        
        return $result['count'] > 0;
    }
    
    /**
     * Cap nhat so luong ton kho
     */
    public static function updateStock($id, $quantity) {
        self::init();
        
        return self::$db->execute(
            "UPDATE products SET stock_quantity = ?, updated_at = NOW() WHERE id = ?",
            [$quantity, $id]
        );
    }
    
    /**
     * Giam so luong ton kho (dung khi ban hang)
     */
    public static function decreaseStock($id, $quantity) {
        self::init();
        
        return self::$db->execute(
            "UPDATE products 
             SET stock_quantity = stock_quantity - ?, updated_at = NOW() 
             WHERE id = ? AND stock_quantity >= ?",
            [$quantity, $id, $quantity]
        );
    }
    
    /**
     * Tang so luong ton kho (dung khi nhap hang hoac huy don)
     */
    public static function increaseStock($id, $quantity) {
        self::init();
        
        return self::$db->execute(
            "UPDATE products 
             SET stock_quantity = stock_quantity + ?, updated_at = NOW() 
             WHERE id = ?",
            [$quantity, $id]
        );
    }
    
    /**
     * Lay tong so san pham
     */
    public static function getTotalCount() {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT COUNT(*) as count FROM products"
        )['count'];
    }
    
    /**
     * Lay san pham sap het hang (< 10)
     */
    public static function getLowStockProducts($limit = 10) {
        self::init();
        
        return self::$db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock_quantity < 10 AND p.stock_quantity > 0
             ORDER BY p.stock_quantity ASC
             LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Lay san pham het hang
     */
    public static function getOutOfStockProducts() {
        self::init();
        
        return self::$db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock_quantity = 0
             ORDER BY p.name ASC"
        );
    }
    
    /**
     * Lay tong gia tri ton kho
     */
    public static function getTotalStockValue() {
        self::init();
        
        $result = self::$db->fetchOne(
            "SELECT SUM(import_price * stock_quantity) as total FROM products"
        );
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Tim san pham theo keyword (dung cho POS)
     */
    public static function search($keyword, $limit = 20) {
        self::init();
        
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.stock_quantity > 0 
                AND (p.barcode LIKE ? OR p.name LIKE ?)
                ORDER BY p.name ASC
                LIMIT ?";
        
        $searchTerm = '%' . $keyword . '%';
        
        return self::$db->fetchAll($sql, [$searchTerm, $searchTerm, $limit]);
    }
}