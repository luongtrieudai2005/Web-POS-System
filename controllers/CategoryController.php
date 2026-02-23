<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    
    /**
     * Hiển thị danh sách danh mục sản phẩm
     */
    public static function index() {
        Auth::requireAdmin();
        
        $search = Helper::get('search', '');
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        
        $categories = Category::getAll($filters);
        
        require_once __DIR__ . '/../views/categories/index.php';
    }
    
    /**
     * Thêm danh mục sản phẩm mới
     */
    public static function create() {
        Auth::requireAdmin();
        
        $errors = [];
        $formData = [];
        
        if (Helper::isPost()) {
            $formData = [
                'name' => Helper::post('name', ''),
                'description' => Helper::post('description', '')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'name' => 'required|min:2|max:100'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (Category::nameExists($formData['name'])) {
                    $errors['name'] = ['Tên danh mục đã tồn tại'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $categoryId = Category::create($formData);
                    
                    if ($categoryId) {
                        Session::setFlash('success', 'Thêm danh mục thành công', 'success');
                        Router::redirect(Router::url('categories/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Có lỗi xảy ra khi thêm danh mục';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/categories/create.php';
    }
    
    /**
     * Sửa thông tin danh mục sản phẩm
     */
    public static function edit($id) {
        Auth::requireAdmin();
        
        $category = Category::getById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Không tìm thấy danh mục', 'danger');
            Router::redirect(Router::url('categories/index.php'));
            exit;
        }
        
        $errors = [];
        
        if (Helper::isPost()) {
            $formData = [
                'name' => Helper::post('name', ''),
                'description' => Helper::post('description', '')
            ];
            
            $validator = new Validator($_POST);
            $validator->validate([
                'name' => 'required|min:2|max:100'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (Category::nameExists($formData['name'], $id)) {
                    $errors['name'] = ['Tên danh mục đã tồn tại'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $result = Category::update($id, $formData);
                    
                    if ($result) {
                        Session::setFlash('success', 'Cập nhật danh mục thành công', 'success');
                        Router::redirect(Router::url('categories/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Có lỗi xảy ra khi cập nhật';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/categories/edit.php';
    }
    
    /**
     * Xóa danh mục sản phẩm
     */
    public static function delete($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('categories/index.php'));
            exit;
        }
        
        $category = Category::getById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Không tìm thấy danh mục', 'danger');
            Router::redirect(Router::url('categories/index.php'));
            exit;
        }
        
        try {
            $result = Category::delete($id);
            
            if ($result) {
                Session::setFlash('success', 'Xóa danh mục thành công', 'success');
            } else {
                Session::setFlash('error', 'Có lỗi xảy ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('categories/index.php'));
        exit;
    }
}