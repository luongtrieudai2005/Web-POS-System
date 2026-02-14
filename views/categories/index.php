<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly danh muc - <?php echo APP_NAME; ?></title>
    
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
                    <span class="badge bg-warning text-dark">Admin</span>
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
                <h4 class="mb-0">Danh sach danh muc</h4>
                <a href="<?php echo Router::url('categories/create.php'); ?>" class="btn btn-add">
                    + Them danh muc moi
                </a>
            </div>
            
            <div class="card-body">
                <form method="GET" action="<?php echo Router::url('categories/index.php'); ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tim kiem theo ten hoac mo ta..." 
                                   value="<?php echo Helper::escape($search ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tim kiem</button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?php echo Router::url('categories/index.php'); ?>" class="btn btn-secondary w-100">
                                Xoa loc
                            </a>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="8%">ID</th>
                                <th width="20%">Ten danh muc</th>
                                <th width="35%">Mo ta</th>
                                <th width="15%">Nguoi tao</th>
                                <th width="12%">Ngay tao</th>
                                <th width="10%">Hanh dong</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        Chua co danh muc nao
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
                                                echo '<em class="text-muted">Khong co mo ta</em>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo Helper::escape($category['creator_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo Helper::formatDate($category['created_at'], 'd/m/Y'); ?></td>
                                        <td>
                                            <a href="<?php echo Router::url('categories/edit.php?id=' . $category['id']); ?>" 
                                               class="btn btn-sm btn-warning btn-action">
                                                Sua
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-action"
                                                    onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo Helper::escape($category['name']); ?>')">
                                                Xoa
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
                        Tong so: <strong><?php echo count($categories); ?></strong> danh muc
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
            if (confirm('Ban co chac chan muon xoa danh muc "' + name + '"?\n\nChi co the xoa danh muc chua co san pham nao.')) {
                const form = document.getElementById('deleteForm');
                form.action = '<?php echo Router::url('categories/delete.php?id='); ?>' + id;
                form.submit();
            }
        }
    </script>
</body>
</html>