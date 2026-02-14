<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Them nhan vien - <?php echo APP_NAME; ?></title>
    
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
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
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
                        <h4 class="mb-0">Them nhan vien moi</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="info-box">
                            <h6 class="mb-2">Luu y:</h6>
                            <ul class="mb-0" style="font-size: 14px;">
                                <li>Email phai la Gmail de nhan duoc thu dang nhap</li>
                                <li>Mat khau tam thoi: <strong><?php echo PASSWORD_TEMP; ?></strong></li>
                                <li>Nhan vien se nhan email voi lien ket dang nhap (hieu luc <?php echo TOKEN_EXPIRY_MINUTES; ?> phut)</li>
                                <li>Sau khi dang nhap, nhan vien bat buoc phai doi mat khau</li>
                            </ul>
                        </div>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo Helper::escape($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo Router::url('users/create'); ?>" id="createUserForm">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Ho va ten <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="<?php echo Helper::escape($formData['full_name'] ?? ''); ?>"
                                       placeholder="Nguyen Van A"
                                       required
                                       autofocus>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['full_name'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email (Gmail) <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo Helper::escape($formData['email'] ?? ''); ?>"
                                       placeholder="nguyenvana@gmail.com"
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['email'][0]); ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">
                                    Email nay se duoc su dung lam ten dang nhap (phan truoc dau @)
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Dien thoai</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo Helper::escape($formData['phone'] ?? ''); ?>"
                                       placeholder="0912345678">
                            </div>
                            
                            <div class="mb-4">
                                <label for="role" class="form-label">Vai tro <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="salesperson" <?php echo ($formData['role'] ?? 'salesperson') == 'salesperson' ? 'selected' : ''; ?>>
                                        Nhan vien ban hang
                                    </option>
                                    <option value="admin" <?php echo ($formData['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>
                                        Quan tri vien
                                    </option>
                                </select>
                                <?php if (isset($errors['role'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['role'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-submit">
                                    Tao nhan vien va gui email
                                </button>
                                <a href="<?php echo Router::url('users'); ?>" class="btn btn-secondary">
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
    
    <script>
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            
            if (!email.endsWith('@gmail.com')) {
                if (!confirm('Email khong phai Gmail. Ban co muon tiep tuc?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</body>
</html>
