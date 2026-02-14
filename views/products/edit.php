<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm - <?php echo APP_NAME; ?></title>
    
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
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .price-info {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        
        .product-image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            margin-top: 10px;
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
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Chinh sua san pham</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="info-box">
                            <strong>Thong tin:</strong>
                            <ul class="mb-0 mt-2" style="font-size: 14px;">
                                <li>ID san pham: <strong><?php echo $product['id']; ?></strong></li>
                                <li>Nguoi tao: <?php echo Helper::escape($product['creator_name'] ?? 'N/A'); ?></li>
                                <li>Ngay tao: <?php echo Helper::formatDate($product['created_at'], 'd/m/Y H:i'); ?></li>
                                <?php if ($product['updated_at'] != $product['created_at']): ?>
                                    <li>Cap nhat gan nhat: <?php echo Helper::formatDate($product['updated_at'], 'd/m/Y H:i'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo Helper::escape($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo Router::url('products/edit.php?id=' . $product['id']); ?>" id="editProductForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="barcode" class="form-label">
                                            Barcode <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control <?php echo isset($errors['barcode']) ? 'is-invalid' : ''; ?>" 
                                               id="barcode" 
                                               name="barcode" 
                                               value="<?php echo Helper::escape($product['barcode']); ?>"
                                               required
                                               autofocus>
                                        <?php if (isset($errors['barcode'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo Helper::escape($errors['barcode'][0]); ?>
                                            </div>
                                        <?php endif; ?>
                                        <small class="form-text text-muted">
                                            Barcode phai la duy nhat
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Ten san pham <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo Helper::escape($product['name']); ?>"
                                               required>
                                        <?php if (isset($errors['name'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo Helper::escape($errors['name'][0]); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">
                                            Danh muc <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" 
                                                id="category_id" 
                                                name="category_id" 
                                                required>
                                            <option value="">Chon danh muc</option>
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
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
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">
                                            So luong ton kho <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control <?php echo isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>" 
                                               id="stock_quantity" 
                                               name="stock_quantity" 
                                               value="<?php echo Helper::escape($product['stock_quantity']); ?>"
                                               min="0"
                                               required>
                                        <?php if (isset($errors['stock_quantity'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo Helper::escape($errors['stock_quantity'][0]); ?>
                                            </div>
                                        <?php endif; ?>
                                        <small class="form-text text-muted">
                                            So luong hien tai: <strong><?php echo $product['stock_quantity']; ?></strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="import_price" class="form-label">
                                            Gia nhap <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control <?php echo isset($errors['import_price']) ? 'is-invalid' : ''; ?>" 
                                               id="import_price" 
                                               name="import_price" 
                                               value="<?php echo Helper::escape($product['import_price']); ?>"
                                               min="0"
                                               step="1000"
                                               required>
                                        <?php if (isset($errors['import_price'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo Helper::escape($errors['import_price'][0]); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="price-info" id="importPriceFormatted"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="retail_price" class="form-label">
                                            Gia ban <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control <?php echo isset($errors['retail_price']) ? 'is-invalid' : ''; ?>" 
                                               id="retail_price" 
                                               name="retail_price" 
                                               value="<?php echo Helper::escape($product['retail_price']); ?>"
                                               min="0"
                                               step="1000"
                                               required>
                                        <?php if (isset($errors['retail_price'])): ?>
                                            <div class="invalid-feedback">
                                                <?php echo Helper::escape($errors['retail_price'][0]); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="price-info" id="retailPriceFormatted"></div>
                                        <div class="price-info" id="profitInfo"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label">Mo ta</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"><?php echo Helper::escape($product['description'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">
                                    Thong tin chi tiet ve cau hinh, tinh nang, mau sac...
                                </small>
                            </div>
                            
                            <?php if (!empty($product['image'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Hinh anh hien tai</label>
                                    <div>
                                        <img src="<?php echo Router::url($product['image']); ?>" 
                                             class="product-image-preview" 
                                             alt="Product">
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-submit">
                                    Cap nhat
                                </button>
                                <a href="<?php echo Router::url('products/index.php'); ?>" class="btn btn-secondary">
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
        function formatMoney(number) {
            return new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
            }).format(number);
        }
        
        function updatePriceDisplay() {
            const importPrice = parseFloat(document.getElementById('import_price').value) || 0;
            const retailPrice = parseFloat(document.getElementById('retail_price').value) || 0;
            
            if (importPrice > 0) {
                document.getElementById('importPriceFormatted').textContent = formatMoney(importPrice);
            } else {
                document.getElementById('importPriceFormatted').textContent = '';
            }
            
            if (retailPrice > 0) {
                document.getElementById('retailPriceFormatted').textContent = formatMoney(retailPrice);
                
                if (importPrice > 0) {
                    const profit = retailPrice - importPrice;
                    const profitPercent = ((profit / importPrice) * 100).toFixed(2);
                    
                    let profitClass = 'text-success';
                    let profitText = 'Loi nhuan: ' + formatMoney(profit) + ' (' + profitPercent + '%)';
                    
                    if (profit <= 0) {
                        profitClass = 'text-danger';
                        profitText = 'Canh bao: Gia ban phai lon hon gia nhap!';
                    }
                    
                    document.getElementById('profitInfo').innerHTML = '<span class="' + profitClass + '">' + profitText + '</span>';
                }
            } else {
                document.getElementById('retailPriceFormatted').textContent = '';
                document.getElementById('profitInfo').textContent = '';
            }
        }
        
        document.getElementById('import_price').addEventListener('input', updatePriceDisplay);
        document.getElementById('retail_price').addEventListener('input', updatePriceDisplay);
        
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            const importPrice = parseFloat(document.getElementById('import_price').value) || 0;
            const retailPrice = parseFloat(document.getElementById('retail_price').value) || 0;
            
            if (retailPrice <= importPrice) {
                e.preventDefault();
                alert('Gia ban phai lon hon gia nhap!');
                return false;
            }
        });
        
        updatePriceDisplay();
    </script>
</body>
</html>