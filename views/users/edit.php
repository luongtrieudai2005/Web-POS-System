<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chinh sua nhan vien - <?php echo APP_NAME; ?></title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Chinh sua thong tin nhan vien</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo Helper::escape($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo Router::url('users/edit.php?id=' . $user['id']); ?>">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Ho va ten <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="<?php echo Helper::escape($user['full_name']); ?>"
                                       required>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['full_name'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo Helper::escape($user['email']); ?>"
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['email'][0]); ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">
                                    Thay doi email se thay doi ten dang nhap
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Dien thoai</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo Helper::escape($user['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Vai tro <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                                        id="role" 
                                        name="role" 
                                        required
                                        <?php echo $user['id'] == 1 ? 'disabled' : ''; ?>>
                                    <option value="salesperson" <?php echo $user['role'] == 'salesperson' ? 'selected' : ''; ?>>
                                        Nhan vien ban hang
                                    </option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>
                                        Quan tri vien
                                    </option>
                                </select>
                                <?php if ($user['id'] == 1): ?>
                                    <input type="hidden" name="role" value="admin">
                                    <small class="form-text text-muted">Khong the thay doi vai tro admin chinh</small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="form-label">Trang thai <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" 
                                        id="status" 
                                        name="status" 
                                        required
                                        <?php echo $user['id'] == 1 ? 'disabled' : ''; ?>>
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
                                <?php if ($user['id'] == 1): ?>
                                    <input type="hidden" name="status" value="active">
                                    <small class="form-text text-muted">Khong the thay doi trang thai admin chinh</small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Cap nhat
                                </button>
                                <a href="<?php echo Router::url('users/detail.php?id=' . $user['id']); ?>" class="btn btn-secondary">
                                    Huy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
