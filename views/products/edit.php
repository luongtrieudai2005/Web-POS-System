<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f4f6fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .content-wrapper {
            padding: 50px 0;
        }

        .card {
            border: 1px solid #e6e9f2;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 18px 22px;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 32px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .info-box {
            background: #eef1ff;
            border: 1px solid #dde3ff;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .info-box ul {
            margin-bottom: 0;
            padding-left: 18px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        .price-info {
            font-size: 13px;
            margin-top: 4px;
        }

        .image-preview-wrapper {
            border: 1px solid #e0e4f5;
            border-radius: 8px;
            padding: 10px;
            display: inline-block;
            background: #fff;
        }

        .product-image-preview {
            max-width: 140px;
            max-height: 140px;
            border-radius: 6px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 9px 22px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-submit:hover {
            opacity: 0.95;
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
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Chỉnh sửa sản phẩm</h5>
                </div>

                <div class="card-body">

                    <div class="info-box">
                        <div class="section-title">Thông tin hệ thống</div>
                        <ul>
                            <li>ID sản phẩm: <strong><?php echo $product['id']; ?></strong></li>
                            <li>Người tạo: <?php echo Helper::escape($product['creator_name'] ?? 'N/A'); ?></li>
                            <li>Ngày tạo: <?php echo Helper::formatDate($product['created_at'], 'd/m/Y H:i'); ?></li>
                            <?php if ($product['updated_at'] != $product['created_at']): ?>
                                <li>Cập nhật gần nhất: <?php echo Helper::formatDate($product['updated_at'], 'd/m/Y H:i'); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo Helper::escape($errors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="<?php echo Router::url('products/edit.php?id=' . $product['id']); ?>"
                          id="editProductForm">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode *</label>
                                <input type="text"
                                       name="barcode"
                                       class="form-control <?php echo isset($errors['barcode']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($product['barcode']); ?>"
                                       required autofocus>
                                <?php if (isset($errors['barcode'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo Helper::escape($errors['barcode'][0]); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="price-info text-muted">
                                    Barcode phải là duy nhất trong hệ thống
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên sản phẩm *</label>
                                <input type="text"
                                       name="name"
                                       class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($product['name']); ?>"
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
                                <label class="form-label">Danh mục *</label>
                                <select name="category_id"
                                        class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>"
                                        required>
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo Helper::escape($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lượng tồn kho *</label>
                                <input type="number"
                                       name="stock_quantity"
                                       min="0"
                                       class="form-control <?php echo isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo Helper::escape($product['stock_quantity']); ?>"
                                       required>
                                <div class="price-info text-muted">
                                    Hiện tại: <?php echo $product['stock_quantity']; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá nhập *</label>
                                <input type="number"
                                       id="import_price"
                                       name="import_price"
                                       min="0"
                                       step="1000"
                                       class="form-control"
                                       value="<?php echo Helper::escape($product['import_price']); ?>"
                                       required>
                                <div class="price-info text-muted" id="importPriceFormatted"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá bán *</label>
                                <input type="number"
                                       id="retail_price"
                                       name="retail_price"
                                       min="0"
                                       step="1000"
                                       class="form-control"
                                       value="<?php echo Helper::escape($product['retail_price']); ?>"
                                       required>
                                <div class="price-info text-muted" id="retailPriceFormatted"></div>
                                <div class="price-info" id="profitInfo"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description"
                                      rows="4"
                                      class="form-control"><?php echo Helper::escape($product['description'] ?? ''); ?></textarea>
                        </div>

                        <?php if (!empty($product['image'])): ?>
                            <div class="mb-4">
                                <label class="form-label">Hình ảnh hiện tại</label>
                                <div class="image-preview-wrapper">
                                    <img src="<?php echo Router::url($product['image']); ?>"
                                         class="product-image-preview"
                                         alt="Product">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-submit text-white">
                                Cập nhật
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