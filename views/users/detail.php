<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiet nhan vien - <?php echo APP_NAME; ?></title>
    
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
        
        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Thong tin nhan vien</h4>
                        
                        <?php if ($user['is_first_login'] == 1): ?>
                            <div class="warning-box">
                                <strong>Chu y:</strong> Nhan vien nay chua dang nhap lan dau.
                                <br>
                                <a href="<?php echo Router::url('users/resend-email.php?id=' . $user['id']); ?>" 
                                   class="btn btn-sm btn-warning mt-2"
                                   onclick="return confirm('Gui lai email dang nhap?');">
                                    Gui lai email
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">ID</div>
                                <div class="col-md-8 info-value"><?php echo $user['id']; ?></div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Ho va ten</div>
                                <div class="col-md-8 info-value">
                                    <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Email</div>
                                <div class="col-md-8 info-value"><?php echo Helper::escape($user['email']); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Ten dang nhap</div>
                                <div class="col-md-8 info-value"><?php echo Helper::escape($user['username']); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Dien thoai</div>
                                <div class="col-md-8 info-value"><?php echo Helper::escape($user['phone'] ?? '-'); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Vai tro</div>
                                <div class="col-md-8 info-value">
                                    <?php if ($user['role'] == 'admin'): ?>
                                        <span class="badge bg-danger">Quan tri vien</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Nhan vien ban hang</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Trang thai</div>
                                <div class="col-md-8 info-value">
                                    <?php if ($user['status'] == 'active'): ?>
                                        <span class="badge bg-success">Hoat dong</span>
                                    <?php elseif ($user['status'] == 'inactive'): ?>
                                        <span class="badge bg-secondary">Ngung hoat dong</span>
                                    <?php else: ?>
                                        <span class="badge bg-dark">Khoa</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Ngay tao</div>
                                <div class="col-md-8 info-value">
                                    <?php echo Helper::formatDate($user['created_at'], 'd/m/Y H:i'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Cap nhat gan nhat</div>
                                <div class="col-md-8 info-value">
                                    <?php echo Helper::formatDate($user['updated_at'], 'd/m/Y H:i'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex gap-2">
                            <a href="<?php echo Router::url('users/edit.php?id=' . $user['id']); ?>" 
                               class="btn btn-primary">
                                Chinh sua
                            </a>
                            <a href="<?php echo Router::url('users/index.php'); ?>" 
                               class="btn btn-secondary">
                                Quay lai
                            </a>
                            
                            <?php if ($user['id'] != 1 && $user['id'] != Auth::id()): ?>
                                <button type="button" 
                                        class="btn btn-danger ms-auto" 
                                        onclick="deleteUser(<?php echo $user['id']; ?>)">
                                    Xoa
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Hanh dong</h5>
                        
                        <?php if ($user['is_first_login'] == 1): ?>
                            <a href="<?php echo Router::url('users/resend-email.php?id=' . $user['id']); ?>" 
                               class="btn btn-warning w-100 mb-2"
                               onclick="return confirm('Gui lai email dang nhap?');">
                                Gui lai email
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user['id'] != 1): ?>
                            <form method="POST" action="<?php echo Router::url('users/change-status.php?id=' . $user['id']); ?>" class="mb-2">
                                <select name="status" class="form-select mb-2">
                                    <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>
                                        Hoat dong
                                    </option>
                                    <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>
                                        Ngung hoat dong
                                    </option>
                                    <option value="locked" <?php echo $user['status'] == 'locked' ? 'selected' : ''; ?>>
                                        Khoa
                                    </option>
                                </select>
                                <button type="submit" class="btn btn-info w-100">
                                    Doi trang thai
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function deleteUser(userId) {
            if (confirm('Ban co chac chan muon xoa nhan vien nay?')) {
                const form = document.getElementById('deleteForm');
                form.action = '<?php echo Router::url('users/delete.php?id='); ?>' + userId;
                form.submit();
            }
        }
    </script>
</body>
</html>
