<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .content-wrapper {
            padding: 35px 0;
        }
        
        .card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 18px 20px;
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
        
        .table thead th {
            background: #f1f3f5;
            font-weight: 600;
            font-size: 14px;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .btn-action {
            padding: 4px 10px;
            font-size: 13px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

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
            <h5 class="mb-0">Danh sách danh mục</h5>
            <a href="<?php echo Router::url('categories/create.php'); ?>" class="btn btn-add text-white">
                + Thêm danh mục
            </a>
        </div>
        
        <div class="card-body">

            <form method="GET" action="<?php echo Router::url('categories/index.php'); ?>" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Tìm kiếm theo tên hoặc mô tả..."
                               value="<?php echo Helper::escape($search ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Tìm kiếm
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?php echo Router::url('categories/index.php'); ?>" 
                           class="btn btn-secondary w-100">
                            Xóa lọc
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="22%">Tên danh mục</th>
                            <th width="32%">Mô tả</th>
                            <th width="15%">Người tạo</th>
                            <th width="13%">Ngày tạo</th>
                            <th width="10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Chưa có danh mục nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td>
                                        <strong><?php echo Helper::escape($category['name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($category['description']) {
                                            echo Helper::escape(Helper::truncate($category['description'], 80));
                                        } else {
                                            echo '<span class="text-muted">Không có mô tả</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo Helper::escape($category['creator_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo Helper::formatDate($category['created_at'], 'd/m/Y'); ?></td>
                                    <td>
                                        <a href="<?php echo Router::url('categories/edit.php?id=' . $category['id']); ?>" 
                                           class="btn btn-sm btn-warning btn-action">
                                            Sửa
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-action"
                                                onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo Helper::escape($category['name']); ?>')">
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <p class="text-muted mb-0">
                    Tổng số: <strong><?php echo count($categories); ?></strong> danh mục
                </p>
            </div>

        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function deleteCategory(id, name) {
        if (confirm('Bạn có chắc chắn muốn xóa danh mục "' + name + '"?\n\nChỉ có thể xóa danh mục chưa có sản phẩm nào.')) {
            const form = document.getElementById('deleteForm');
            form.action = '<?php echo Router::url('categories/delete.php?id='); ?>' + id;
            form.submit();
        }
    }
</script>

</body>
</html>