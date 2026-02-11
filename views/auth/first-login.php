<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doi mat khau - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
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
        
        .password-container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .password-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .password-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .password-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .password-body {
            padding: 40px 30px;
        }
        
        .welcome-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .welcome-box h5 {
            margin: 0 0 10px 0;
            color: #333;
            font-weight: 600;
        }
        
        .welcome-box p {
            margin: 0;
            color: #666;
            font-size: 14px;
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
            border-color: #f5576c;
            box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
        }
        
        .btn-change-password {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-change-password:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
        }
        
        .btn-logout {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        .password-requirements {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .password-requirements h6 {
            margin: 0 0 10px 0;
            color: #856404;
            font-weight: 600;
            font-size: 14px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #856404;
        }
        
        .password-requirements ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-container">
            <div class="password-card">
                <!-- Header -->
                <div class="password-header">
                    <h2>Tao mat khau moi</h2>
                    <p>Lan dang nhap dau tien</p>
                </div>
                
                <!-- Body -->
                <div class="password-body">
                    <!-- Welcome message -->
                    <div class="welcome-box">
                        <h5>Chao mung, <?php echo Helper::escape($user['full_name']); ?>!</h5>
                        <p>
                            Day la lan dau tien ban dang nhap vao he thong. 
                            Vi ly do bao mat, ban can tao mat khau moi de tiep tuc su dung he thong.
                        </p>
                    </div>
                    
                    <!-- Thong bao loi chung -->
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Loi!</strong> <?php echo Helper::escape($errors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form doi mat khau -->
                    <form method="POST" action="<?php echo Router::url('first-login.php'); ?>" id="changePasswordForm">
                        <!-- Mat khau moi -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mat khau moi</label>
                            <input 
                                type="password" 
                                class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" 
                                id="new_password" 
                                name="new_password" 
                                placeholder="Nhap mat khau moi"
                                minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                                required
                                autofocus
                            >
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['new_password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Xac nhan mat khau -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Xac nhan mat khau</label>
                            <input 
                                type="password" 
                                class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="Nhap lai mat khau moi"
                                minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                                required
                            >
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['confirm_password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-change-password">
                            Tao mat khau moi
                        </button>
                        
                        <!-- Logout Button -->
                        <a href="<?php echo Router::url('logout'); ?>" class="btn btn-logout">
                            Dang xuat
                        </a>
                    </form>
                    
                    <!-- Yeu cau mat khau -->
                    <div class="password-requirements">
                        <h6>Yeu cau mat khau:</h6>
                        <ul>
                            <li>Toi thieu <?php echo PASSWORD_MIN_LENGTH; ?> ky tu</li>
                            <li>Nen ket hop chu, so va ky tu dac biet</li>
                            <li>Khong nen su dung mat khau qua don gian</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validation Script -->
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            var newPassword = document.getElementById('new_password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            
            // Kiem tra mat khau khop nhau
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Mat khau xac nhan khong khop!');
                return false;
            }
            
            // Kiem tra do dai
            if (newPassword.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
                e.preventDefault();
                alert('Mat khau phai co it nhat <?php echo PASSWORD_MIN_LENGTH; ?> ky tu!');
                return false;
            }
        });
    </script>
</body>
</html>