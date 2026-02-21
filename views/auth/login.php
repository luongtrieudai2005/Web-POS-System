<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo APP_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            font-size: 13px;
            color: #666;
        }
        
        .icon-input {
            position: relative;
        }
        
        .icon-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .icon-input .form-control {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <h2><?php echo APP_NAME; ?></h2>
                    <p>Hệ thống quản lý bán hàng</p>
                </div>
                
                <!-- Body -->
                <div class="login-body">
                    <!-- Thong bao loi token -->
                    <?php if (isset($errors['token'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Lỗi</strong> <?php echo Helper::escape($errors['token']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Thong bao loi dang nhap -->
                    <?php if (isset($errors['login'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Lỗi!</strong> <?php echo Helper::escape($errors['login']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Flash message -->
                    <?php if (Session::hasFlash('success')): ?>
                        <?php $flash = Session::getFlash('success'); ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo Helper::escape($flash['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form dang nhap -->
                    <form method="POST" action="<?php echo Router::url('login'); ?>">
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input 
                                type="text" 
                                class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                id="username" 
                                name="username" 
                                value="<?php echo Helper::escape($username); ?>"
                                placeholder="Nhập tên đăng nhập"
                                autofocus
                                required
                            >
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['username'][0]); ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Ví dụ: admin (phần trước dấu @ của email)
                            </small>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input 
                                type="password" 
                                class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                id="password" 
                                name="password" 
                                placeholder="Nhập mật khẩu"
                                required
                            >
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-login">
                            Đăng nhập
                        </button>
                    </form>
                    <!-- Huong dan -->
                    <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 10px; border-left: 4px solid #667eea;">
                        <p class="mb-2" style="font-size: 14px; font-weight: 600; color: #333;">
                            Thông tin đăng nhập mặc định:
                        </p>
                        <ul class="mb-0" style="font-size: 13px; color: #666;">
                            <li>Tên đăng nhập: <strong>admin</strong></li>
                            <li>Mật khẩu: <strong>admin</strong></li>
                        </ul>
                        <hr style="margin: 15px 0;">
                        <p class="mb-0" style="font-size: 13px; color: #666;">
                            <strong>Nhân viên mới:</strong> Vui lòng đăng nhập bằng liên kết trong email.
                        </p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="login-footer">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> -->

