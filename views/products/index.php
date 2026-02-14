<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly san pham - <?php echo APP_NAME; ?></title>
    
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
        
        .content-wrapper {
            padding: 30px 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .btn-action {
            padding: 5px 12px;
            font-size: 13px;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .stock-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .stock-low {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-out {
            background: #f8d7da;
            color: #721c24;
        }
        
        .stock-good {
            background: #d4edda;
            color: #155724;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?php echo Router::url('dashboard'); ?>">
                <?php echo APP_NAME; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <?php echo Helper::escape(Auth::user()['full_name']); ?>
                    <?php if (Auth::isAdmin()): ?>
                        <span class="badge bg-warning text-dark">Admin</span>
                    <?php else: ?>
                        <span class="badge bg-info">Nhan vien</span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo Router::url('dashboard'); ?>" class="btn btn-outline-light btn-sm me-2">
                    Dashboard
                </a>
                <a href="<?php echo Router::url('logout'); ?>" class="btn btn-outline-light btn-sm">
                    Dang xuat
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container content-wrapper">
        <?php if (Session::hasFlash('success')): ?>
            <?php $flash = Session::getFlash('success'); ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo Helper::escape($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (Session::hasFlash('error')): ?>
            <?php $flash = Session::getFlash('error'); ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo Helper::escape($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Danh sach san pham</h4>
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?php echo Router::url('products/create.php'); ?>" class="btn btn-add">
                        + Them san pham moi
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <form method="GET" action="<?php echo Router::url('products/index.php'); ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tim kiem theo barcode, ten san pham..." 
                                   value="<?php echo Helper::escape($search ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="category_id" class="form-select">
                                <option value="">Tat ca danh muc</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo (isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo Helper::escape($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tim kiem</button>
                        </div>
                        <div class="col-md-1">
                            <a href="<?php echo Router::url('products/index.php'); ?>" class="btn btn-secondary w-100">
                                Xoa
                            </a>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="10%">Barcode</th>
                                <th width="20%">Ten san pham</th>
                                <th width="12%">Danh muc</th>
                                <?php if (Auth::isAdmin()): ?>
                                    <th width="10%">Gia nhap</th>
                                <?php endif; ?>
                                <th width="10%">Gia ban</th>
                                <th width="8%">Ton kho</th>
                                <?php if (Auth::isAdmin()): ?>
                                    <th width="10%">Ngay tao</th>
                                    <th width="15%">Hanh dong</th>
                                <?php else: ?>
                                    <th width="12%">Ngay tao</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="<?php echo Auth::isAdmin() ? '9' : '6'; ?>" class="text-center py-4">
                                        Chua co san pham nao
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <code><?php echo Helper::escape($product['barcode']); ?></code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="<?php echo Router::url($product['image']); ?>" 
                                                         class="product-image me-2" 
                                                         alt="Product">
                                                <?php endif; ?>
                                                <strong><?php echo Helper::escape($product['name']); ?></strong>
                                            </div>
                                        </td>
                                        <td><?php echo Helper::escape($product['category_name'] ?? '-'); ?></td>
                                        
                                        <?php if (Auth::isAdmin()): ?>
                                            <td>
                                                <span class="text-muted">
                                                    <?php echo Helper::formatMoney($product['import_price']); ?>
                                                </span>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td>
                                            <strong class="text-success">
                                                <?php echo Helper::formatMoney($product['retail_price']); ?>
                                            </strong>
                                        </td>
                                        
                                        <td>
                                            <?php
                                            $stock = $product['stock_quantity'];
                                            if ($stock == 0) {
                                                echo '<span class="stock-badge stock-out">Het hang</span>';
                                            } elseif ($stock < 10) {
                                                echo '<span class="stock-badge stock-low">' . $stock . '</span>';
                                            } else {
                                                echo '<span class="stock-badge stock-good">' . $stock . '</span>';
                                            }
                                            ?>
                                        </td>
                                        
                                        <td><?php echo Helper::formatDate($product['created_at'], 'd/m/Y'); ?></td>
                                        
                                        <?php if (Auth::isAdmin()): ?>
                                            <td>
                                                <a href="<?php echo Router::url('products/edit.php?id=' . $product['id']); ?>" 
                                                   class="btn btn-sm btn-warning btn-action">
                                                    Sua
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger btn-action"
                                                        onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo Helper::escape($product['name']); ?>')">
                                                    Xoa
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted mb-0">
                        Tong so: <strong><?php echo count($products); ?></strong> san pham
                        <?php if (!empty($products) && Auth::isAdmin()): ?>
                            <span class="ms-3">|</span>
                            <span class="ms-3">Tong gia tri ton kho: 
                                <strong class="text-success">
                                    <?php 
                                    $totalValue = 0;
                                    foreach ($products as $p) {
                                        $totalValue += $p['import_price'] * $p['stock_quantity'];
                                    }
                                    echo Helper::formatMoney($totalValue);
                                    ?>
                                </strong>
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (Auth::isAdmin()): ?>
        <form id="deleteForm" method="POST" style="display: none;">
            <input type="hidden" name="action" value="delete">
        </form>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (Auth::isAdmin()): ?>
    <script>
        function deleteProduct(id, name) {
            if (confirm('Ban co chac chan muon xoa san pham "' + name + '"?\n\nChi co the xoa san pham chua co trong don hang nao.')) {
                const form = document.getElementById('deleteForm');
                form.action = '<?php echo Router::url('products/delete.php?id='); ?>' + id;
                form.submit();
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>