<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .content-wrapper {
            padding: 50px 0;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 22px 28px;
            border-radius: 14px 14px 0 0;
        }

        .card-body {
            padding: 32px 30px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.15);
        }

        .form-text {
            font-size: 13px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 26px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-submit:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            border-radius: 8px;
            padding: 10px 22px;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thêm danh mục mới</h5>
                </div>

                <div class="card-body">

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo Helper::escape($errors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo Router::url('categories/create.php'); ?>">

                        <div class="mb-4">
                            <label for="name" class="form-label">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                   id="name"
                                   name="name"
                                   value="<?php echo Helper::escape($formData['name'] ?? ''); ?>"
                                   placeholder="Ví dụ: Điện thoại, Phụ kiện..."
                                   required
                                   autofocus>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['name'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Nhập mô tả ngắn cho danh mục này..."><?php echo Helper::escape($formData['description'] ?? ''); ?></textarea>
                            <div class="form-text">
                                Thông tin này giúp phân biệt các danh mục trong hệ thống.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-submit">
                                Thêm danh mục
                            </button>
                            <a href="<?php echo Router::url('categories/index.php'); ?>" class="btn btn-secondary">
                                Hủy
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