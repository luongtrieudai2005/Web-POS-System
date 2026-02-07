<?php
/**
 * Dashboard Page
 * Trang chu sau khi dang nhap
 */

// QUAN TRONG: Load bootstrap TRUOC TIEN!
require_once __DIR__ . '/../config/bootstrap.php';

// Kiem tra da dang nhap chua
Auth::requireLogin();

// Neu la first login thi bat buoc doi mat khau
if (Auth::requirePasswordChange()) {
    Router::redirect(Router::url('first-login.php'));
    exit;
}

// Lay thong tin user
$user = Auth::user();

// Lay thong tin thong ke don gian
$db = Database::getInstance();

// Dem so luong
$totalProducts = $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'];
$totalCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM customers")['count'];
$totalOrders = $db->fetchOne("SELECT COUNT(*) as count FROM orders")['count'];

// Tinh tong doanh thu
$revenue = $db->fetchOne("SELECT IFNULL(SUM(total_amount), 0) as total FROM orders");
$totalRevenue = $revenue['total'];

// Don hang hom nay
$todayOrders = $db->fetchOne(
    "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()"
)['count'];

// Doanh thu hom nay
$todayRevenue = $db->fetchOne(
    "SELECT IFNULL(SUM(total_amount), 0) as total FROM orders WHERE DATE(created_at) = CURDATE()"
)['total'];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stats-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin: 10px 0;
        }
        
        .stats-label {
            color: #666;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><?php echo APP_NAME; ?></a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    Xin chào, <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                    <?php if (Auth::isAdmin()): ?>
                        <span class="badge bg-warning text-dark">Admin</span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo Router::url('logout.php'); ?>" class="btn btn-outline-light btn-sm">
                    Đăng xuất
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-5">
        <!-- Flash Message -->
        <?php if (Session::hasFlash('success')): ?>
            <?php $flash = Session::getFlash('success'); ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo Helper::escape($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <h2 class="mb-4">Trang chủ</h2>
        
        <!-- Thong ke -->
        <div class="row g-4">
            <!-- Tong san pham -->
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        SP
                    </div>
                    <div class="stats-label">Tổng sản phẩm</div>
                    <div class="stats-value"><?php echo number_format($totalProducts); ?></div>
                </div>
            </div>
            
            <!-- Tong khach hang -->
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        KH
                    </div>
                    <div class="stats-label">Tổng khách hàng</div>
                    <div class="stats-value"><?php echo number_format($totalCustomers); ?></div>
                </div>
            </div>
            
            <!-- Tong don hang -->
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        DH
                    </div>
                    <div class="stats-label">Tổng đơn hàng</div>
                    <div class="stats-value"><?php echo number_format($totalOrders); ?></div>
                </div>
            </div>
            
            <!-- Tong doanh thu -->
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        DT
                    </div>
                    <div class="stats-label">Tổng doanh thu</div>
                    <div class="stats-value" style="font-size: 20px;">
                        <?php echo Helper::formatMoney($totalRevenue); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thong ke hom nay -->
        <h4 class="mt-5 mb-3">Hôm nay</h4>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <div class="stats-label">Đơn hàng hôm nay</div>
                    <div class="stats-value text-primary"><?php echo number_format($todayOrders); ?></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stats-card">
                    <div class="stats-label">Doanh thu hôm nay</div>
                    <div class="stats-value text-success" style="font-size: 24px;">
                        <?php echo Helper::formatMoney($todayRevenue); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thong bao -->
        <div class="mt-5">
            <div class="alert alert-info">
                <h5>Chức năng đang phát triển</h5>
                <p class="mb-0">
                    Đây chỉ là trang Dashboard đơn giản. Các chức năng chính sẽ được phát triển ở các bước tiếp theo:
                </p>
                <ul class="mt-2">
                    <li>Quản lý nhân viên (Admin)</li>
                    <li>Quản lý sản phẩm</li>
                    <li>Giao dịch bán hàng (POS)</li>
                    <li>Báo cáo thống kê</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>