<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên - <?php echo APP_NAME; ?></title>
    
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
                <h4 class="mb-0">Danh sách nhân viên</h4>
                <a href="<?php echo Router::url('users/create'); ?>" class="btn btn-add">
                    + Thêm nhân viên mới
                </a>
            </div>
            
            <div class="card-body">
                <form method="GET" action="<?php echo Router::url('users'); ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm theo tên, email..." 
                                   value="<?php echo Helper::escape($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" <?php echo ($role ?? '') == 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                                <option value="salesperson" <?php echo ($role ?? '') == 'salesperson' ? 'selected' : ''; ?>>Nhân viên</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" <?php echo ($status ?? '') == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo ($status ?? '') == 'inactive' ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                                <option value="locked" <?php echo ($status ?? '') == 'locked' ? 'selected' : ''; ?>>Khóa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Điện thoại</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        Không có nhân viên nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                                            <?php if ($user['is_first_login'] == 1): ?>
                                                <br><small class="text-warning">Chưa đăng nhập lần đầu</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo Helper::escape($user['email']); ?></td>
                                        <td><?php echo Helper::escape($user['phone'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($user['role'] == 'admin'): ?>
                                                <span class="badge bg-danger">Quản trị viên</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Nhân viên</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] == 'active'): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php elseif ($user['status'] == 'inactive'): ?>
                                                <span class="badge bg-secondary">Ngừng</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark">Khóa</span>
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
                                                Sửa
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
                        Tổng số: <strong><?php echo count($users); ?></strong> nhân viên
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>