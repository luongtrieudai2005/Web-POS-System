<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Order.php';

class ReportController {

    public static function index() {
        Auth::requireLogin();
        if (Auth::requirePasswordChange()) {
            Router::redirect(Router::url('first-login'));
            exit;
        }

        // Xu ly range mac dinh: hom nay
        $range = Helper::get('range', 'today');
        $dateFrom = Helper::get('date_from', '');
        $dateTo = Helper::get('date_to', '');

        switch ($range) {
            case 'today':
                $dateFrom = date('Y-m-d');
                $dateTo = date('Y-m-d');
                break;
            case 'yesterday':
                $dateFrom = date('Y-m-d', strtotime('-1 day'));
                $dateTo = date('Y-m-d', strtotime('-1 day'));
                break;
            case '7days':
                $dateFrom = date('Y-m-d', strtotime('-6 days'));
                $dateTo = date('Y-m-d');
                break;
            case 'this_month':
                $dateFrom = date('Y-m-01');
                $dateTo = date('Y-m-d');
                break;
            case 'custom':
                if (!$dateFrom || !$dateTo) {
                    $dateFrom = date('Y-m-d');
                    $dateTo = date('Y-m-d');
                }
                break;
            default:
                $dateFrom = date('Y-m-d');
                $dateTo = date('Y-m-d');
        }

        $employeeId = Auth::isAdmin() ? null : Auth::id();
        $report = Order::getReport($dateFrom, $dateTo, $employeeId);
        $chartData = Order::getRevenueByDay($dateFrom, $dateTo);
        $topProducts = Order::getTopProducts($dateFrom, $dateTo, 5);

        require_once __DIR__ . '/../views/reports/index.php';
    }

    public static function orderDetail($id) {
        Auth::requireLogin();
        $order = Order::getById($id);
        if (!$order) {
            Session::setFlash('error', 'Không tìm thấy đơn hàng', 'danger');
            Router::redirect(Router::url('reports/index.php'));
            exit;
        }
        // Nhan vien chi xem don hang cua minh
        if (!Auth::isAdmin() && $order['employee_id'] != Auth::id()) {
            Session::setFlash('error', 'Bạn không có quyền xem đơn hàng này', 'danger');
            Router::redirect(Router::url('reports/index.php'));
            exit;
        }
        $details = Order::getDetails($id);
        require_once __DIR__ . '/../views/reports/order_detail.php';
    }
}
