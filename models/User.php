<?php
require_once __DIR__ . '/../config/bootstrap.php';

class User {
    
    private static $db;
    
    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    public static function getAll($filters = []) {
        self::init();
        
        $sql = "SELECT id, username, email, full_name, phone, role, status, 
                       is_first_login, created_at 
                FROM users 
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR username LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        return self::$db->fetchAll($sql, $params);
    }
    
    public static function getById($id) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
    }
    
    public static function getByEmail($email) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
    }
    
    public static function getByUsername($username) {
        self::init();
        
        return self::$db->fetchOne(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );
    }
    
    public static function create($data) {
        self::init();
        
        $username = explode('@', $data['email'])[0];
        
        $sql = "INSERT INTO users (
                    username, email, password, full_name, phone, 
                    role, status, is_first_login, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $username,
            $data['email'],
            $data['password'],
            $data['full_name'],
            $data['phone'] ?? null,
            $data['role'] ?? 'salesperson',
            $data['status'] ?? 'active',
            1,
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
        
        $sql = "UPDATE users SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['full_name'])) {
            $updates[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
            
            $updates[] = "username = ?";
            $params[] = explode('@', $data['email'])[0];
        }
        
        if (isset($data['phone'])) {
            $updates[] = "phone = ?";
            $params[] = $data['phone'];
        }
        
        if (isset($data['role'])) {
            $updates[] = "role = ?";
            $params[] = $data['role'];
        }
        
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (isset($data['password'])) {
            $updates[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $updates[] = "updated_at = NOW()";
        
        $sql .= implode(', ', $updates);
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return self::$db->execute($sql, $params);
    }
    
    public static function delete($id) {
        self::init();
        
        return self::$db->execute(
            "DELETE FROM users WHERE id = ? AND id != 1",
            [$id]
        );
    }
    
    public static function changeStatus($id, $status) {
        self::init();
        
        return self::$db->execute(
            "UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?",
            [$status, $id]
        );
    }
    
    public static function getTotalCount($role = null) {
        self::init();
        
        if ($role) {
            return self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM users WHERE role = ?",
                [$role]
            )['count'];
        }
        
        return self::$db->fetchOne(
            "SELECT COUNT(*) as count FROM users"
        )['count'];
    }
    
    public static function emailExists($email, $excludeId = null) {
        self::init();
        
        if ($excludeId) {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM users WHERE email = ? AND id != ?",
                [$email, $excludeId]
            );
        } else {
            $result = self::$db->fetchOne(
                "SELECT COUNT(*) as count FROM users WHERE email = ?",
                [$email]
            );
        }
        
        return $result['count'] > 0;
    }
}
