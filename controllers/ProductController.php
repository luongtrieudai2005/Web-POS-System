<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController {
    
    /**
     * Hien thi danh sach san pham
     * Ca admin va nhan vien deu xem duoc, nhung hien thi khac nhau
     */
    public static function index() {
        Auth::requireLogin();
        
        $search = Helper::get('search', '');
        $categoryId = Helper::get('category_id', '');
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        if ($categoryId) $filters['category_id'] = $categoryId;
        
        $products = Product::getAll($filters);
        
        $categories = Category::getForDropdown();
        
        require_once __DIR__ . '/../views/products/index.php';
    }
    
    /**
     * Hien thi form them san pham (CHI ADMIN)
     */
    public static function create() {
        Auth::requireAdmin();
        
        $errors = [];
        $formData = [];
        
        if (Helper::isPost()) {
            $formData = [
                'barcode' => Helper::post('barcode', ''),
                'name' => Helper::post('name', ''),
                'category_id' => Helper::post('category_id', ''),
                'import_price' => Helper::post('import_price', ''),
                'retail_price' => Helper::post('retail_price', ''),
                'stock_quantity' => Helper::post('stock_quantity', 0),
                'description' => Helper::post('description', '')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'barcode' => 'required|min:5|max:50',
                'name' => 'required|min:3|max:200',
                'category_id' => 'required|numeric',
                'import_price' => 'required|numeric',
                'retail_price' => 'required|numeric',
                'stock_quantity' => 'required|numeric'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (Product::barcodeExists($formData['barcode'])) {
                    $errors['barcode'] = ['Barcode da ton tai trong he thong'];
                }
                
                if ($formData['retail_price'] <= $formData['import_price']) {
                    $errors['retail_price'] = ['Gia ban phai lon hon gia nhap'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $productId = Product::create($formData);
                    
                    if ($productId) {
                        Session::setFlash('success', 'Them san pham thanh cong', 'success');
                        Router::redirect(Router::url('products/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi them san pham';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        $categories = Category::getForDropdown();
        
        require_once __DIR__ . '/../views/products/create.php';
    }
    
    /**
     * Hien thi form chinh sua san pham (CHI ADMIN)
     */
    public static function edit($id) {
        Auth::requireAdmin();
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Khong tim thay san pham', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $errors = [];
        
        if (Helper::isPost()) {
            $formData = [
                'barcode' => Helper::post('barcode', ''),
                'name' => Helper::post('name', ''),
                'category_id' => Helper::post('category_id', ''),
                'import_price' => Helper::post('import_price', ''),
                'retail_price' => Helper::post('retail_price', ''),
                'stock_quantity' => Helper::post('stock_quantity', 0),
                'description' => Helper::post('description', '')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'barcode' => 'required|min:5|max:50',
                'name' => 'required|min:3|max:200',
                'category_id' => 'required|numeric',
                'import_price' => 'required|numeric',
                'retail_price' => 'required|numeric',
                'stock_quantity' => 'required|numeric'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (Product::barcodeExists($formData['barcode'], $id)) {
                    $errors['barcode'] = ['Barcode da ton tai trong he thong'];
                }
                
                if ($formData['retail_price'] <= $formData['import_price']) {
                    $errors['retail_price'] = ['Gia ban phai lon hon gia nhap'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $result = Product::update($id, $formData);
                    
                    if ($result) {
                        Session::setFlash('success', 'Cap nhat san pham thanh cong', 'success');
                        Router::redirect(Router::url('products/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi cap nhat';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        $categories = Category::getForDropdown();
        
        require_once __DIR__ . '/../views/products/edit.php';
    }
    
    /**
     * Xoa san pham (CHI ADMIN)
     */
    public static function delete($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Khong tim thay san pham', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        try {
            $result = Product::delete($id);
            
            if ($result) {
                Session::setFlash('success', 'Xoa san pham thanh cong', 'success');
            } else {
                Session::setFlash('error', 'Co loi xay ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('products/index.php'));
        exit;
    }
    
    /**
     * Tim kiem san pham (dung cho POS)
     * API endpoint tra ve JSON
     */
    public static function search() {
        Auth::requireLogin();
        
        if (!Helper::isAjax()) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
        
        $keyword = Helper::get('keyword', '');
        
        if (empty($keyword)) {
            echo json_encode([]);
            exit;
        }
        
        try {
            $products = Product::search($keyword);
            
            $results = [];
            foreach ($products as $product) {
                $results[] = [
                    'id' => $product['id'],
                    'barcode' => $product['barcode'],
                    'name' => $product['name'],
                    'category_name' => $product['category_name'],
                    'retail_price' => $product['retail_price'],
                    'stock_quantity' => $product['stock_quantity'],
                    'image' => $product['image']
                ];
            }
            
            echo json_encode($results);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Lay thong tin chi tiet san pham theo barcode (dung cho POS)
     * API endpoint tra ve JSON
     */
    public static function getByBarcode($barcode) {
        Auth::requireLogin();
        
        if (!Helper::isAjax()) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
        
        try {
            $product = Product::getByBarcode($barcode);
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'San pham khong ton tai']);
                exit;
            }
            
            if ($product['stock_quantity'] <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'San pham da het hang']);
                exit;
            }
            
            echo json_encode([
                'id' => $product['id'],
                'barcode' => $product['barcode'],
                'name' => $product['name'],
                'category_name' => $product['category_name'],
                'retail_price' => $product['retail_price'],
                'stock_quantity' => $product['stock_quantity'],
                'image' => $product['image']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Cap nhat so luong ton kho (dung cho admin quan ly kho)
     */
    public static function updateStock($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Khong tim thay san pham', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $newQuantity = Helper::post('stock_quantity', 0);
        
        if ($newQuantity < 0) {
            Session::setFlash('error', 'So luong khong hop le', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        try {
            $result = Product::updateStock($id, $newQuantity);
            
            if ($result) {
                Session::setFlash('success', 'Cap nhat ton kho thanh cong', 'success');
            } else {
                Session::setFlash('error', 'Co loi xay ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('products/index.php'));
        exit;
    }
}