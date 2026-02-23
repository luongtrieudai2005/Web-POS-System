<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';

class CustomerController {

    public static function index() {
        Auth::requireLogin();
        if (Auth::requirePasswordChange()) {
            Router::redirect(Router::url('first-login'));
            exit;
        }

        $search = Helper::get('search', '');
        $filters = [];
        if ($search) $filters['search'] = $search;

        $customers = Customer::getAll($filters);
        require_once __DIR__ . '/../views/customers/index.php';
    }

    public static function detail($id) {
        Auth::requireLogin();
        $customer = Customer::getById($id);
        if (!$customer) {
            Session::setFlash('error', 'Không tìm thấy khách hàng', 'danger');
            Router::redirect(Router::url('customers/index.php'));
            exit;
        }
        $orders = Customer::getOrderHistory($id);
        require_once __DIR__ . '/../views/customers/detail.php';
    }
}
