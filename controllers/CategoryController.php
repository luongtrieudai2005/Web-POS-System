<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    
    public static function index() {
        Auth::requireAdmin();
        
        $search = Helper::get('search', '');
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        
        $categories = Category::getAll($filters);
        
        require_once __DIR__ . '/../views/categories/index.php';
    }
    
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
                    $errors['name'] = ['Ten danh muc da ton tai'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $categoryId = Category::create($formData);
                    
                    if ($categoryId) {
                        Session::setFlash('success', 'Them danh muc thanh cong', 'success');
                        Router::redirect(Router::url('categories/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi them danh muc';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/categories/create.php';
    }
    
    public static function edit($id) {
        Auth::requireAdmin();
        
        $category = Category::getById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Khong tim thay danh muc', 'danger');
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
                    $errors['name'] = ['Ten danh muc da ton tai'];
                }
            }
            
            if (empty($errors)) {
                try {
                    $result = Category::update($id, $formData);
                    
                    if ($result) {
                        Session::setFlash('success', 'Cap nhat danh muc thanh cong', 'success');
                        Router::redirect(Router::url('categories/index.php'));
                        exit;
                    } else {
                        $errors['general'] = 'Co loi xay ra khi cap nhat';
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../views/categories/edit.php';
    }
    
    public static function delete($id) {
        Auth::requireAdmin();
        
        if (!Helper::isPost()) {
            Router::redirect(Router::url('categories/index.php'));
            exit;
        }
        
        $category = Category::getById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Khong tim thay danh muc', 'danger');
            Router::redirect(Router::url('categories/index.php'));
            exit;
        }
        
        try {
            $result = Category::delete($id);
            
            if ($result) {
                Session::setFlash('success', 'Xoa danh muc thanh cong', 'success');
            } else {
                Session::setFlash('error', 'Co loi xay ra', 'danger');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage(), 'danger');
        }
        
        Router::redirect(Router::url('categories/index.php'));
        exit;
    }
}
