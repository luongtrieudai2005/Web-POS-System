<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa danh mục - <?php echo APP_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .content-wrapper {
            padding: 40px 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.07);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 18px 22px;
            border-radius: 12px 12px 0 0;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 6px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.1rem rgba(102,126,234,0.15);
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-submit:hover {
            opacity: 0.95;
        }

        .info-box {
            background: #f1f5ff;
            border: 1px solid #d6e0ff;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .info-box ul {
            padding-left: 18px;
            margin-bottom: 0;
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
                    <h5 class="mb-0">Chỉnh sửa danh mục</h5>
                </div>

                <div class="card-body p-4">

                    <div class="info-box">
                        <strong>Thông tin danh mục</strong>
                        <ul>
                            <li>Người tạo: <?php echo Helper::escape($category['creator_name'] ?? 'N/A'); ?></li>
                            <li>Ngày tạo: <?php echo Helper::formatDate($category['created_at'], 'd/m/Y H:i'); ?></li>
                        </ul>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo Helper::escape($errors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo Router::url('categories/edit.php?id=' . $category['id']); ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                   id="name"
                                   name="name"
                                   value="<?php echo Helper::escape($category['name']); ?>"
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
                                      rows="4"><?php echo Helper::escape($category['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-submit">
                                Cập nhật
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