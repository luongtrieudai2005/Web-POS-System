<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Them danh muc - <?php echo APP_NAME; ?></title>
    
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
                        <h4 class="mb-0">Them danh muc moi</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo Helper::escape($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo Router::url('categories/create.php'); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Ten danh muc <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo Helper::escape($formData['name'] ?? ''); ?>"
                                       placeholder="Dien thoai, Phu kien, Tablet..."
                                       required
                                       autofocus>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['name'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label">Mo ta</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Mo ta ve danh muc nay..."><?php echo Helper::escape($formData['description'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">
                                    Mo ta nay se giup phan biet cac danh muc
                                </small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-submit">
                                    Them danh muc
                                </button>
                                <a href="<?php echo Router::url('categories/index.php'); ?>" class="btn btn-secondary">
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