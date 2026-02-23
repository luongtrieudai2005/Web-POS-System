<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết nhân viên - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap -->
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
        
        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container content-wrapper">

    <!-- Flash success -->
    <?php if (Session::hasFlash('success')): ?>
        <?php $flash = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo Helper::escape($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Flash error -->
    <?php if (Session::hasFlash('error')): ?>
        <?php $flash = Session::getFlash('error'); ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo Helper::escape($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        
        <!-- Thông tin nhân viên -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-4">
                    
                    <h4 class="mb-4">Thông tin nhân viên</h4>

                    <?php if ($user['is_first_login'] == 1): ?>
                        <div class="warning-box">
                            <strong>Chú ý:</strong> Nhân viên này chưa đăng nhập lần đầu.
                            <br>
                            <a href="<?php echo Router::url('users/resend-email.php?id=' . $user['id']); ?>"
                               class="btn btn-sm btn-warning mt-2"
                               onclick="return confirm('Gửi lại email đăng nhập?');">
                                Gửi lại email
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- ID -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">ID</div>
                            <div class="col-md-8 info-value">
                                <?php echo $user['id']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Họ và tên -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Họ và tên</div>
                            <div class="col-md-8 info-value">
                                <strong><?php echo Helper::escape($user['full_name']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Email</div>
                            <div class="col-md-8 info-value">
                                <?php echo Helper::escape($user['email']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Tên đăng nhập</div>
                            <div class="col-md-8 info-value">
                                <?php echo Helper::escape($user['username']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Điện thoại -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Điện thoại</div>
                            <div class="col-md-8 info-value">
                                <?php echo Helper::escape($user['phone'] ?? '-'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Vai trò -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Vai trò</div>
                            <div class="col-md-8 info-value">
                                <?php if ($user['role'] == 'admin'): ?>
                                    <span class="badge bg-danger">Quản trị viên</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Nhân viên bán hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Trạng thái</div>
                            <div class="col-md-8 info-value">
                                <?php if ($user['status'] == 'active'): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php elseif ($user['status'] == 'inactive'): ?>
                                    <span class="badge bg-secondary">Ngừng hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-dark">Khóa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Ngày tạo -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Ngày tạo</div>
                            <div class="col-md-8 info-value">
                                <?php echo Helper::formatDate($user['created_at'], 'd/m/Y H:i'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Cập nhật -->
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Cập nhật gần nhất</div>
                            <div class="col-md-8 info-value">
                                <?php echo Helper::formatDate($user['updated_at'], 'd/m/Y H:i'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-4 d-flex gap-2">
                        <a href="<?php echo Router::url('users/edit.php?id=' . $user['id']); ?>"
                           class="btn btn-primary">
                            Chỉnh sửa
                        </a>

                        <a href="<?php echo Router::url('users/index.php'); ?>"
                           class="btn btn-secondary">
                            Quay lại
                        </a>

                        <?php if ($user['id'] != 1 && $user['id'] != Auth::id()): ?>
                            <button type="button"
                                    class="btn btn-danger ms-auto"
                                    onclick="deleteUser(<?php echo $user['id']; ?>)">
                                Xóa
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <!-- Sidebar hành động -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">Hành động</h5>

                    <!-- Nút xem doanh số -->
                    <a href="<?php echo Router::url('users/sales_report.php?id=' . $user['id']); ?>"
                       class="btn w-100 mb-2 text-white fw-semibold"
                       style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        Xem doanh số bán hàng
                    </a>

                    <?php if ($user['is_first_login'] == 1): ?>
                        <a href="<?php echo Router::url('users/resend-email.php?id=' . $user['id']); ?>"
                           class="btn btn-warning w-100 mb-2"
                           onclick="return confirm('Gửi lại email đăng nhập?');">
                            Gửi lại email
                        </a>
                    <?php endif; ?>

                    <?php if ($user['id'] != 1): ?>
                        <form method="POST"
                              action="<?php echo Router::url('users/change-status.php?id=' . $user['id']); ?>"
                              class="mb-2">
                            <select name="status" class="form-select mb-2">
                                <option value="active"   <?php echo $user['status'] == 'active'   ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                                <option value="locked"   <?php echo $user['status'] == 'locked'   ? 'selected' : ''; ?>>Khóa</option>
                            </select>
                            <button type="submit" class="btn btn-info w-100 text-white">
                                Đổi trạng thái
                            </button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Form xóa -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
</form>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function deleteUser(userId) {
    if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?php echo Router::url('users/delete.php?id='); ?>' + userId;
        form.submit();
    }
}
</script>

</body>
</html>