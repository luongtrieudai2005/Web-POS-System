<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f3f5fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .content-wrapper {
            padding: 50px 0;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #6c757d;
        }

        .card {
            border: 1px solid #e5e9f2;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.04);
        }

        .card-body {
            padding: 30px;
        }

        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-add:hover {
            opacity: 0.95;
        }

        .filter-box {
            background: #f8f9ff;
            border: 1px solid #e3e8ff;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 25px;
        }

        .table thead th {
            background: #f7f8fc;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #6c757d;
        }

        .table td {
            vertical-align: middle;
        }

        .product-image {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e5e5;
        }

        .stock-badge {
            padding: 4px 10px;
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

        .summary-bar {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e9f2;
            font-size: 14px;
            color: #6c757d;
        }

        .summary-bar strong {
            color: #212529;
        }

        .btn-action {
            padding: 4px 10px;
            font-size: 13px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<?php
    $activePage = 'products';
    require_once __DIR__ . '/../layouts/navbar.php'; 
?>

<div class="container content-wrapper">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">Danh sách sản phẩm</div>
            <div class="page-subtitle">
                Quản lý thông tin sản phẩm, tồn kho và giá bán
            </div>
        </div>

        <?php if (Auth::isAdmin()): ?>
            <a href="<?php echo Router::url('products/create.php'); ?>" 
               class="btn btn-add text-white">
                + Thêm sản phẩm mới
            </a>
        <?php endif; ?>
    </div>

    <?php if (Session::hasFlash('success')): ?>
        <?php $flash = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
            <?php echo Helper::escape($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <div class="filter-box">
                <form method="GET" action="<?php echo Router::url('products/index.php'); ?>">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Tìm theo mã vạch hoặc tên sản phẩm..."
                                   value="<?php echo Helper::escape($search ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <select name="category_id" class="form-select">
                                <option value="">-- Tất cả danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                        <?php echo (isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo Helper::escape($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Tìm kiếm
                            </button>
                        </div>

                        <div class="col-md-1">
                            <a href="<?php echo Router::url('products/index.php'); ?>" 
                               class="btn btn-outline-secondary w-100">
                                Xóa lọc
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã vạch</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <?php if (Auth::isAdmin()): ?>
                                <th>Giá nhập</th>
                            <?php endif; ?>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Ngày tạo</th>
                            <?php if (Auth::isAdmin()): ?>
                                <th>Hành động</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="<?php echo Auth::isAdmin() ? '9' : '7'; ?>" 
                                    class="text-center py-5 text-muted">
                                    Chưa có sản phẩm nào trong danh sách
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><code><?php echo Helper::escape($product['barcode']); ?></code></td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="<?php echo Router::url($product['image']); ?>"
                                                     class="product-image me-2"
                                                     alt="<?php echo Helper::escape($product['name']); ?>">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo Helper::escape($product['name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?php echo Helper::escape($product['category_name'] ?? '—'); ?></td>

                                    <?php if (Auth::isAdmin()): ?>
                                        <td class="text-muted">
                                            <?php echo Helper::formatMoney($product['import_price']); ?>
                                        </td>
                                    <?php endif; ?>

                                    <td class="text-success fw-semibold">
                                        <?php echo Helper::formatMoney($product['retail_price']); ?>
                                    </td>

                                    <td>
                                        <?php
                                            $stock = $product['stock_quantity'];
                                            if ($stock == 0) {
                                                echo '<span class="stock-badge stock-out">Hết hàng</span>';
                                            } elseif ($stock < 10) {
                                                echo '<span class="stock-badge stock-low">' . number_format($stock) . '</span>';
                                            } else {
                                                echo '<span class="stock-badge stock-good">' . number_format($stock) . '</span>';
                                            }
                                        ?>
                                    </td>

                                    <td>
                                        <?php echo Helper::formatDate($product['created_at'], 'd/m/Y'); ?>
                                    </td>

                                    <?php if (Auth::isAdmin()): ?>
                                        <td>
                                            <a href="<?php echo Router::url('products/edit.php?id=' . $product['id']); ?>"
                                               class="btn btn-sm btn-outline-warning btn-action">
                                                Sửa
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-action"
                                                    onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo Helper::escape(addslashes($product['name'])); ?>')">
                                                Xóa
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-bar d-flex justify-content-between">
                <div>
                    Hiển thị: <strong><?php echo count($products); ?></strong> sản phẩm
                </div>
                <?php if (!empty($products) && Auth::isAdmin()): ?>
                    <?php
                        $totalValue = 0;
                        foreach ($products as $p) {
                            $totalValue += $p['import_price'] * $p['stock_quantity'];
                        }
                    ?>
                    <div>
                        Tổng giá trị tồn kho: 
                        <strong class="text-primary"><?php echo Helper::formatMoney($totalValue); ?></strong>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bạn có thể thêm script xóa sản phẩm nếu chưa có -->
<script>
function deleteProduct(id, name) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm "' + name + '"?\nHành động này không thể hoàn tác.')) {
        window.location.href = '<?php echo Router::url('products/delete.php?id='); ?>' + id;
    }
}
</script>

</body>
</html>