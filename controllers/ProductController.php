<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController {
    
    /**
     * Hiển thị danh sách sản phẩm
     * Cả admin và nhân viên đều xem được, nhưng hiển thị khác nhau
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
     * Hiển thị form thêm sản phẩm mới (CHỈ ADMIN)
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
                    $errors['barcode'] = ['Mã vạch đã tồn tại trong hệ thống'];
                }
                
                if ($formData['retail_price'] <= $formData['import_price']) {
                    $errors['retail_price'] = ['Giá bán phải lớn hơn giá nhập'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $productId = Product::create($formData);
                    
                    if ($productId) {
                        Session::setFlash('success', 'Thêm sản phẩm thành công', 'success');
                        Router::redirect(Router::url('products/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Có lỗi xảy ra khi thêm sản phẩm';
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
     * Hiển thị form chỉnh sửa sản phẩm (CHỈ ADMIN)
     */
    public static function edit($id) {
        Auth::requireAdmin();
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Không tìm thấy sản phẩm', 'danger');
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
                    $errors['barcode'] = ['Mã vạch đã tồn tại trong hệ thống'];
                }
                
                if ($formData['retail_price'] <= $formData['import_price']) {
                    $errors['retail_price'] = ['Giá bán phải lớn hơn giá nhập'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $result = Product::update($id, $formData);
                    
                    if ($result) {
                        Session::setFlash('success', 'Cập nhật sản phẩm thành công', 'success');
                        Router::redirect(Router::url('products/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Có lỗi xảy ra khi cập nhật';
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
     * Xóa sản phẩm (CHỈ ADMIN)
     */
    public static function delete($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Không tìm thấy sản phẩm', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        try {
            $result = Product::delete($id);
            
            if ($result) {
                Session::setFlash('success', 'Xóa sản phẩm thành công', 'success');
            } else {
                Session::setFlash('error', 'Có lỗi xảy ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('products/index.php'));
        exit;
    }
    
    /**
     * Tìm kiếm sản phẩm (dùng cho POS)
     * API endpoint trả về JSON
     */
    public static function search() {
        Auth::requireLogin();
        
        if (!Helper::isAjax()) {
            http_response_code(400);
            echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
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
     * Lấy thông tin chi tiết sản phẩm theo mã vạch (dùng cho POS)
     * API endpoint trả về JSON
     */
    public static function getByBarcode($barcode) {
        Auth::requireLogin();
        
        if (!Helper::isAjax()) {
            http_response_code(400);
            echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
            exit;
        }
        
        try {
            $product = Product::getByBarcode($barcode);
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'Sản phẩm không tồn tại']);
                exit;
            }
            
            if ($product['stock_quantity'] <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Sản phẩm đã hết hàng']);
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
     * Cập nhật số lượng tồn kho (dùng cho admin quản lý kho)
     */
    public static function updateStock($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $product = Product::getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Không tìm thấy sản phẩm', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        $newQuantity = Helper::post('stock_quantity', 0);
        
        if ($newQuantity < 0) {
            Session::setFlash('error', 'Số lượng không hợp lệ', 'danger');
            Router::redirect(Router::url('products/index.php'));
            exit;
        }
        
        try {
            $result = Product::updateStock($id, $newQuantity);
            
            if ($result) {
                Session::setFlash('success', 'Cập nhật tồn kho thành công', 'success');
            } else {
                Session::setFlash('error', 'Có lỗi xảy ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('products/index.php'));
        exit;
    }
}