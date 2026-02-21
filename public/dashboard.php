<?php
require_once __DIR__ . '/../config/bootstrap.php';

Auth::requireLogin();

if (Auth::requirePasswordChange()) {
    Router::redirect(Router::url('first-login.php'));
    exit;
}

$db = Database::getInstance();

/* ====== Thống kê tổng quan ====== */
$totalProducts  = $db->fetchOne("SELECT COUNT(*) AS c FROM products")['c'];
$totalCustomers = $db->fetchOne("SELECT COUNT(*) AS c FROM customers")['c'];
$totalOrders    = $db->fetchOne("SELECT COUNT(*) AS c FROM orders")['c'];
$totalRevenue   = $db->fetchOne("SELECT IFNULL(SUM(total_amount),0) AS t FROM orders")['t'];

$todayOrders  = $db->fetchOne("SELECT COUNT(*) AS c FROM orders WHERE DATE(created_at)=CURDATE()")['c'];
$todayRevenue = $db->fetchOne("SELECT IFNULL(SUM(total_amount),0) AS t FROM orders WHERE DATE(created_at)=CURDATE()")['t'];

/* ====== Sản phẩm sắp hết hàng ====== */
$lowStock = $db->fetchAll(
    "SELECT p.name, p.stock_quantity, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.stock_quantity < 10
     ORDER BY p.stock_quantity ASC
     LIMIT 8"
);

/* ====== Đơn hàng gần đây ====== */
$recentOrders = $db->fetchAll(
    "SELECT o.order_code, o.total_amount, o.created_at,
            cu.full_name AS customer_name,
            u.full_name AS employee_name
     FROM orders o
     LEFT JOIN customers cu ON o.customer_id = cu.id
     LEFT JOIN users u ON o.employee_id = u.id
     ORDER BY o.created_at DESC
     LIMIT 8"
);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
</head>

<body>
<?php
    $activePage = 'dashboard';
    require_once __DIR__ . '/../views/layouts/navbar.php';
?>

<div class="container content-wrapper">

    <!-- Flash message -->
    <?php if (Session::hasFlash('success')):
        $f = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $f['type']; ?> alert-dismissible fade show flash-message mb-4">
            <?php echo Helper::escape($f['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Greeting -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">
            Xin chào, <?php echo Helper::escape(Auth::user()['full_name']); ?>!
        </h5>
        <span class="text-muted small">
            <?php echo Helper::formatDate(date('Y-m-d H:i:s'), 'd/m/Y H:i'); ?>
        </span>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row g-3 mb-4">

        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#667eea,#764ba2);">P</div>
                <div class="stat-label">Sản phẩm</div>
                <div class="stat-value"><?php echo number_format($totalProducts); ?></div>
                <a href="<?php echo Router::url('products/index.php'); ?>"
                   class="btn btn-sm btn-outline-primary mt-2">Xem</a>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f093fb,#f5576c);">K</div>
                <div class="stat-label">Khách hàng</div>
                <div class="stat-value"><?php echo number_format($totalCustomers); ?></div>
                <a href="<?php echo Router::url('customers/index.php'); ?>"
                   class="btn btn-sm btn-outline-danger mt-2">Xem</a>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">Đ</div>
                <div class="stat-label">Đơn hàng</div>
                <div class="stat-value"><?php echo number_format($totalOrders); ?></div>
                <a href="<?php echo Router::url('reports/index.php'); ?>"
                   class="btn btn-sm btn-outline-info mt-2">Xem</a>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#43e97b,#38f9d7);">$</div>
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value" style="font-size:18px;">
                    <?php echo Helper::formatMoney($totalRevenue); ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Thống kê hôm nay + sản phẩm sắp hết -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="main-card card h-100">
                <div class="card-header">
                    <h6 class="fw-bold mb-0">Hôm nay</h6>
                </div>
                <div class="card-body">

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Số đơn hàng</span>
                        <strong class="text-primary fs-5"><?php echo $todayOrders; ?></strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 mb-3">
                        <span class="text-muted">Doanh thu</span>
                        <strong class="text-success">
                            <?php echo Helper::formatMoney($todayRevenue); ?>
                        </strong>
                    </div>

                    <a href="<?php echo Router::url('transactions/index.php'); ?>"
                       class="btn btn-primary-grad w-100">
                        Bắt đầu bán hàng
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="main-card card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Sản phẩm sắp hết hàng</h6>
                    <?php if (Auth::isAdmin()): ?>
                        <a href="<?php echo Router::url('products/index.php'); ?>"
                           class="btn btn-sm btn-outline-primary">
                            Quản lý
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($lowStock)): ?>
                        <div class="text-center text-muted py-4">
                            Không có sản phẩm nào sắp hết hàng
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th class="text-center">Tồn kho</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStock as $p): ?>
                                        <tr>
                                            <td><?php echo Helper::escape($p['name']); ?></td>
                                            <td class="text-muted small">
                                                <?php echo Helper::escape($p['category_name'] ?? '-'); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($p['stock_quantity'] == 0): ?>
                                                    <span class="stock-badge stock-out">Hết hàng</span>
                                                <?php else: ?>
                                                    <span class="stock-badge stock-low">
                                                        <?php echo $p['stock_quantity']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

    <!-- Đơn hàng gần đây -->
    <div class="main-card card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Đơn hàng gần đây</h6>
            <a href="<?php echo Router::url('reports/index.php'); ?>"
               class="btn btn-sm btn-outline-secondary">
                Xem báo cáo
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th class="text-end">Tổng tiền</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5"
                                class="text-center py-4 text-muted">
                                Chưa có đơn hàng nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td><code><?php echo Helper::escape($o['order_code']); ?></code></td>
                                <td><?php echo Helper::escape($o['customer_name']); ?></td>
                                <td><?php echo Helper::escape($o['employee_name']); ?></td>
                                <td class="text-end text-success fw-bold">
                                    <?php echo Helper::formatMoney($o['total_amount']); ?>
                                </td>
                                <td>
                                    <?php echo Helper::formatDate($o['created_at'], 'd/m/Y H:i'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>