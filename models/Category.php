<?php
require_once __DIR__ . '/../config/bootstrap.php';

class Category {
    
    private static $db;
    
    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    public static function getAll($filters = []) {
        self::init();
        
        $sql = "SELECT c.*, u.full_name as creator_name 
                FROM categories c
                LEFT JOIN users u ON c.created_by = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return self::$db->fetchAll($sql, $params);
    }
    
    public static function getById($id) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT c.*, u.full_name as creator_name 
             FROM categories c
             LEFT JOIN users u ON c.created_by = u.id
             WHERE c.id = ?",
            [$id]
        );
    }
    
    public static function create($data) {
        self::init();
        
        $sql = "INSERT INTO categories (name, description, created_by, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $params = [
            $data['name'],
            $data['description'] ?? null,
            Auth::id()
        ];
        
        $result = self::$db->execute($sql, $params);
        
        if ($result) {
            return self::$db->lastInsertId();
        }
        
        return false;
    }
    
    public static function update($id, $data) {
        self::init();
        
        $sql = "UPDATE categories SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['name'])) {
            $updates[] = "name = ?";
            $params[] = $data['name'];
        }
        
        if (isset($data['description'])) {
            $updates[] = "description = ?";
            $params[] = $data['description'];
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql .= implode(', ', $updates);
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return self::$db->execute($sql, $params);
    }
    
    public static function delete($id) {
        self::init();
        
        $productCount = self::$db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ?",
            [$id]
        );
        
        if ($productCount['count'] > 0) {
            throw new Exception('Khong the xoa danh muc da co san pham');
        }
        
        return self::$db->execute(
            "DELETE FROM categories WHERE id = ?",
            [$id]
        );
    }
    
    public static function nameExists($name, $excludeId = null) {
        self::init();
        
        if ($excludeId) {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM categories WHERE name = ? AND id != ?",
                [$name, $excludeId]
            );
        } else {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM categories WHERE name = ?",
                [$name]
            );
        }
        
        return $result['count'] > 0;
    }
    
    public static function getTotalCount() {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT COUNT(*) as count FROM categories"
        )['count'];
    }
    
    public static function getForDropdown() {
        self::init();
        
        return self::$db->fetchAll(
            "SELECT id, name FROM categories ORDER BY name ASC"
        );
    }
}
