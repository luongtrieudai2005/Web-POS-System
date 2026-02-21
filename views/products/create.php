<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .content-wrapper {
            padding: 40px 0;
        }
        
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 16px 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .card-body {
            padding: 28px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control,
        .form-select {
            border-radius: 6px;
        }
        
        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: #667eea;
        }
        
        .note-box {
            background: #f1f5ff;
            border: 1px solid #dbe2ff;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 22px;
        }
        
        .note-box ul {
            margin-bottom: 0;
            padding-left: 18px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .btn-submit:hover {
            opacity: 0.95;
        }
        
        .price-info {
            font-size: 13px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<?php
    $activePage = 'products';
    require_once __DIR__ . '/../layouts/navbar.php'; 
?>

<div class="container content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thêm sản phẩm mới</h5>
                </div>
                
                <div class="card-body">

                    <div class="note-box">
                        <strong>Lưu ý:</strong>
                        <ul>
                            <li>Barcode phải là duy nhất trong hệ thống</li>
                            <li>Giá bán nên lớn hơn giá nhập để đảm bảo lợi nhuận</li>
                            <li>Có thể cập nhật tồn kho sau khi tạo sản phẩm</li>
                        </ul>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo Helper::escape($errors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo Router::url('products/create.php'); ?>" id="createProductForm">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="barcode"
                                       class="form-control <?php echo isset($errors['barcode']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($formData['barcode'] ?? ''); ?>"
                                       required autofocus>
                                <?php if (isset($errors['barcode'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['barcode'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($formData['name'] ?? ''); ?>"
                                       required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['name'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id"
                                        class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>"
                                        required>
                                    <option value="">Chọn danh mục</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"
                                                <?php echo (isset($formData['category_id']) && $formData['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo Helper::escape($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($errors['category_id'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['category_id'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="stock_quantity"
                                       min="0"
                                       class="form-control <?php echo isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($formData['stock_quantity'] ?? '0'); ?>"
                                       required>
                                <?php if (isset($errors['stock_quantity'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['stock_quantity'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá nhập <span class="text-danger">*</span></label>
                                <input type="number"
                                       id="import_price"
                                       name="import_price"
                                       min="0"
                                       step="1000"
                                       class="form-control <?php echo isset($errors['import_price']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($formData['import_price'] ?? ''); ?>"
                                       required>
                                <?php if (isset($errors['import_price'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['import_price'][0]); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="price-info text-muted" id="importPriceFormatted"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <input type="number"
                                       id="retail_price"
                                       name="retail_price"
                                       min="0"
                                       step="1000"
                                       class="form-control <?php echo isset($errors['retail_price']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($formData['retail_price'] ?? ''); ?>"
                                       required>
                                <?php if (isset($errors['retail_price'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['retail_price'][0]); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="price-info text-muted" id="retailPriceFormatted"></div>
                                <div class="price-info" id="profitInfo"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description"
                                      rows="4"
                                      class="form-control"><?php echo Helper::escape($formData['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-submit text-white">
                                Thêm sản phẩm
                            </button>
                            <a href="<?php echo Router::url('products/index.php'); ?>" class="btn btn-secondary">
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