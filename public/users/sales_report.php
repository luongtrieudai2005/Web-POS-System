<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/User.php';

// Chỉ admin mới được xem
Auth::requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin nhân viên
$employee = User::getById($id);
if (!$employee) {
    Session::setFlash('error', 'Không tìm thấy nhân viên', 'danger');
    Router::redirect(Router::url('users/index.php'));
    exit;
}

// Xử lý range thời gian (giống ReportController)
$range    = isset($_GET['range']) ? $_GET['range'] : 'this_month';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo   = isset($_GET['date_to'])   ? $_GET['date_to']   : '';

switch ($range) {
    case 'today':
        $dateFrom = date('Y-m-d');
        $dateTo   = date('Y-m-d');
        break;
    case 'yesterday':
        $dateFrom = date('Y-m-d', strtotime('-1 day'));
        $dateTo   = date('Y-m-d', strtotime('-1 day'));
        break;
    case '7days':
        $dateFrom = date('Y-m-d', strtotime('-6 days'));
        $dateTo   = date('Y-m-d');
        break;
    case 'this_month':
        $dateFrom = date('Y-m-01');
        $dateTo   = date('Y-m-d');
        break;
    case 'custom':
        if (!$dateFrom || !$dateTo) {
            $dateFrom = date('Y-m-01');
            $dateTo   = date('Y-m-d');
        }
        break;
    default:
        $dateFrom = date('Y-m-01');
        $dateTo   = date('Y-m-d');
}

// Lấy dữ liệu báo cáo theo nhân viên cụ thể
$report    = Order::getReport($dateFrom, $dateTo, $id);
$summary   = $report['summary'];
$orders    = $report['orders'];
$chartData = Order::getRevenueByDay($dateFrom, $dateTo, $id);

require_once __DIR__ . '/../../views/users/sales_report.php';
