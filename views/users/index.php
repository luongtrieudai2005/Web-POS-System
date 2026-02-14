<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly nhan vien - <?php echo APP_NAME; ?></title>
    
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
        
        .table-responsive {
            border-radius: 10px;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
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
                <h4 class="mb-0">Danh sach nhan vien</h4>
                <a href="<?php echo Router::url('users/create'); ?>" class="btn btn-add">
                    + Them nhan vien moi
                </a>
            </div>
            
            <div class="card-body">
                <form method="GET" action="<?php echo Router::url('users'); ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tim kiem theo ten, email..." 
                                   value="<?php echo Helper::escape($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">Tat ca vai tro</option>
                                <option value="admin" <?php echo ($role ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="salesperson" <?php echo ($role ?? '') == 'salesperson' ? 'selected' : ''; ?>>Nhan vien</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tat ca trang thai</option>
                                <option value="active" <?php echo ($status ?? '') == 'active' ? 'selected' : ''; ?>>Hoat dong</option>
                                <option value="inactive" <?php echo ($status ?? '') == 'inactive' ? 'selected' : ''; ?>>Ngung hoat dong</option>
                                <option value="locked" <?php echo ($status ?? '') == 'locked' ? 'selected' : ''; ?>>Khoa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tim kiem</button>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ho ten</th>
                                <th>Email</th>
                                <th>Dien thoai</th>
                                <th>Vai tro</th>
                                <th>Trang thai</th>
                                <th>Ngay tao</th>
                                <th>Hanh dong</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        Khong co nhan vien nao
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                                            <?php if ($user['is_first_login'] == 1): ?>
                                                <br><small class="text-warning">Chua dang nhap lan dau</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo Helper::escape($user['email']); ?></td>
                                        <td><?php echo Helper::escape($user['phone'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($user['role'] == 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Nhan vien</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] == 'active'): ?>
                                                <span class="badge bg-success">Hoat dong</span>
                                            <?php elseif ($user['status'] == 'inactive'): ?>
                                                <span class="badge bg-secondary">Ngung</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark">Khoa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo Helper::formatDate($user['created_at'], 'd/m/Y'); ?></td>
                                        <td>
                                            <a href="<?php echo Router::url('users/detail.php?id=' . $user['id']); ?>" 
                                               class="btn btn-sm btn-info btn-action">
                                                Xem
                                            </a>
                                            <a href="<?php echo Router::url('users/edit.php?id=' . $user['id']); ?>" 
                                               class="btn btn-sm btn-warning btn-action">
                                                Sua
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted mb-0">
                        Tong so: <strong><?php echo count($users); ?></strong> nhan vien
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
