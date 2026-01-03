-- database/pos_database.sql
DROP DATABASE IF EXISTS pos_system;
CREATE DATABASE pos_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_system;

-- ===================================
-- Bảng users (nhân viên + admin)
-- ===================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    avatar VARCHAR(255),
    role ENUM('admin', 'salesperson') DEFAULT 'salesperson',
    status ENUM('active', 'inactive', 'locked') DEFAULT 'active',
    is_first_login BOOLEAN DEFAULT TRUE,
    login_token VARCHAR(255),
    token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraint
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- ===================================
-- Bảng categories
-- ===================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    
    -- Constraint
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ===================================
-- Bảng products
-- ===================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    barcode VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    category_id INT,
    import_price DECIMAL(15,2) NOT NULL,
    retail_price DECIMAL(15,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_barcode (barcode),
    INDEX idx_category_id (category_id),
    INDEX idx_name (name)
);

-- ===================================
-- Bảng customers
-- ===================================
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(15) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Index
    INDEX idx_phone (phone)
);

-- ===================================
-- Bảng orders
-- ===================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_code VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    employee_id INT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    amount_paid DECIMAL(15,2) NOT NULL,
    change_amount DECIMAL(15,2) NOT NULL,
    total_items INT NOT NULL,
    total_profit DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Constraints
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE RESTRICT,
    
    -- Indexes (QUAN TRỌNG cho Báo cáo!)
    INDEX idx_customer_id (customer_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_created_at (created_at),
    INDEX idx_order_code (order_code)
);

-- ===================================
-- Bảng order_details
-- ===================================
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    import_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    profit DECIMAL(15,2),
    
    -- Constraints
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    
    -- Indexes
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
);

-- ===================================
-- DỮ LIỆU MẪU
-- ===================================

-- 1. Admin mặc định (password: admin)
INSERT INTO users (username, email, password, full_name, role, is_first_login) 
VALUES (
    'admin', 
    'admin@gmail.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Administrator', 
    'admin', 
    FALSE
);

-- 2. Categories mẫu
INSERT INTO categories (name, description, created_by) VALUES
('Điện thoại', 'Điện thoại di động các hãng', 1),
('Phụ kiện', 'Tai nghe, sạc, ốp lưng', 1),
('Tablet', 'Máy tính bảng', 1);

-- 3. Products mẫu
INSERT INTO products (barcode, name, category_id, import_price, retail_price, stock_quantity, created_by) VALUES
('8934567890123', 'iPhone 15 Pro Max 256GB', 1, 25000000, 30000000, 10, 1),
('8934567890124', 'Samsung Galaxy S24 Ultra', 1, 22000000, 27000000, 15, 1),
('8934567890125', 'AirPods Pro Gen 2', 2, 5000000, 6500000, 30, 1),
('8934567890126', 'Sạc nhanh 65W', 2, 300000, 500000, 50, 1),
('8934567890127', 'iPad Pro 11 inch', 3, 18000000, 22000000, 8, 1);

-- 4. Customers mẫu
INSERT INTO customers (phone, full_name, address) VALUES
('0912345678', 'Trần Thị B', 'Hà Nội'),
('0987654321', 'Lê Văn C', 'Hồ Chí Minh');

-- 5. Orders mẫu
INSERT INTO orders (order_code, customer_id, employee_id, total_amount, amount_paid, change_amount, total_items, total_profit, created_at) VALUES
('ORD-20250102-0001', 1, 1, 36500000, 37000000, 500000, 2, 6500000, '2025-01-02 10:30:00'),
('ORD-20250102-0002', 2, 1, 500000, 500000, 0, 1, 200000, '2025-01-02 14:15:00');

-- 6. Order_details mẫu
INSERT INTO order_details (order_id, product_id, quantity, unit_price, import_price, subtotal, profit) VALUES
(1, 1, 1, 30000000, 25000000, 30000000, 5000000),
(1, 3, 1, 6500000, 5000000, 6500000, 1500000),
(2, 4, 1, 500000, 300000, 500000, 200000);