<?php
require_once __DIR__ . '/../config/bootstrap.php';

Auth::requireLogin();

if (Auth::requirePasswordChange()) {
    Router::redirect(Router::url('first-login'));
    exit;
}

$user = Auth::user();
$db = Database::getInstance();

$totalProducts = $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'];
$totalCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM customers")['count'];
$totalOrders = $db->fetchOne("SELECT COUNT(*) as count FROM orders")['count'];

$revenue = $db->fetchOne("SELECT IFNULL(SUM(total_amount), 0) as total FROM orders");
$totalRevenue = $revenue['total'];

$todayOrders = $db->fetchOne(
    "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()"
)['count'];

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
        
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 10px;
            margin-top: 8px;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        .btn-quick {
            width: 100%;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-quick:hover {
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?php echo Router::url('dashboard'); ?>">
                <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo Router::url('dashboard'); ?>">
                            Dashboard
                        </a>
                    </li>
                    
                    <?php if (Auth::isAdmin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Quan ly
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo Router::url('users/index.php'); ?>">
                                    Quan ly nhan vien
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo Router::url('categories/index.php'); ?>">
                                    Danh muc san pham
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo Router::url('products/index.php'); ?>">
                                    Quan ly san pham
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Router::url('transactions/index.php'); ?>">
                            Ban hang (POS)
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Router::url('reports/index.php'); ?>">
                            Bao cao
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <span class="navbar-text text-white me-3">
                        <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                        <?php if (Auth::isAdmin()): ?>
                            <span class="badge bg-warning text-dark">Admin</span>
                        <?php endif; ?>
                    </span>
                    <a href="<?php echo Router::url('logout'); ?>" class="btn btn-outline-light btn-sm">
                        Dang xuat
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <?php if (Session::hasFlash('success')): ?>
            <?php $flash = Session::getFlash('success'); ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo Helper::escape($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <span class="text-muted"><?php echo Helper::formatDate(date('Y-m-d H:i:s'), 'd/m/Y H:i'); ?></span>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        📦
                    </div>
                    <div class="stats-label">Tong san pham</div>
                    <div class="stats-value"><?php echo number_format($totalProducts); ?></div>
                    <a href="<?php echo Router::url('products/index.php'); ?>" class="btn btn-sm btn-outline-primary mt-2">
                        Xem chi tiet
                    </a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        👥
                    </div>
                    <div class="stats-label">Tong khach hang</div>
                    <div class="stats-value"><?php echo number_format($totalCustomers); ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        🛒
                    </div>
                    <div class="stats-label">Tong don hang</div>
                    <div class="stats-value"><?php echo number_format($totalOrders); ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        💰
                    </div>
                    <div class="stats-label">Tong doanh thu</div>
                    <div class="stats-value" style="font-size: 20px;">
                        <?php echo Helper::formatMoney($totalRevenue); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mt-5 mb-3">Hom nay</h4>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <div class="stats-label">Don hang hom nay</div>
                    <div class="stats-value text-primary"><?php echo number_format($todayOrders); ?></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stats-card">
                    <div class="stats-label">Doanh thu hom nay</div>
                    <div class="stats-value text-success" style="font-size: 24px;">
                        <?php echo Helper::formatMoney($todayRevenue); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (Auth::isAdmin()): ?>
        <div class="quick-actions">
            <h5 class="mb-3">Thao tac nhanh</h5>
            <div class="row">
                <div class="col-md-3">
                    <a href="<?php echo Router::url('users/create.php'); ?>" class="btn btn-primary btn-quick">
                        + Them nhan vien
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo Router::url('categories/index.php'); ?>" class="btn btn-info btn-quick">
                        Danh muc
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo Router::url('products/index.php'); ?>" class="btn btn-success btn-quick">
                        Quan ly san pham
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo Router::url('reports/index.php'); ?>" class="btn btn-warning btn-quick">
                        Xem bao cao
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>