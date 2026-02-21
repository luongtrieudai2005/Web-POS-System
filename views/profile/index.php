<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">

    <style>
        .avatar-wrap {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e0e0e0;
        }

        .avatar-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 40px;
            font-weight: 700;
        }
    </style>
</head>

<body>
<?php
    $activePage = 'profile';
    require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container content-wrapper">

    <!-- Flash message -->
    <?php if (Session::hasFlash('success')): 
        $f = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $f['type']; ?> alert-dismissible fade show flash-message mb-4">
            <?php echo Helper::escape($f['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Thông tin cá nhân -->
        <div class="col-md-7">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Thông tin cá nhân</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST"
                          action="<?php echo Router::url('profile/index.php'); ?>"
                          enctype="multipart/form-data">

                        <input type="hidden" name="action" value="update_info">

                        <!-- Avatar -->
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?php echo Router::url($currentUser['avatar']); ?>"
                                     class="avatar-img"
                                     alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="form-label small fw-bold">Ảnh đại diện</label>
                                <input type="file"
                                       name="avatar"
                                       class="form-control form-control-sm"
                                       accept="image/*">
                                <small class="text-muted">
                                    JPG, PNG, GIF (tối đa 5MB)
                                </small>

                                <?php if (isset($errors['avatar'])): ?>
                                    <div class="text-danger small">
                                        <?php echo Helper::escape($errors['avatar'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="full_name"
                                   class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo Helper::escape($currentUser['full_name']); ?>"
                                   required>

                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['full_name'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo Helper::escape($currentUser['email']); ?>"
                                   disabled>
                            <small class="text-muted">
                                Email không thể thay đổi
                            </small>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="<?php echo Helper::escape($currentUser['phone'] ?? ''); ?>">
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   value="<?php echo Helper::escape($currentUser['address'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary-grad">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Đổi mật khẩu -->
        <div class="col-md-5">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Đổi mật khẩu</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST"
                          action="<?php echo Router::url('profile/index.php'); ?>">

                        <input type="hidden" name="action" value="change_password">

                        <!-- Mật khẩu hiện tại -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>"
                                   required>

                            <?php if (isset($errors['current_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['current_password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mật khẩu mới -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="new_password"
                                   minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                                   class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>"
                                   required>

                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['new_password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="confirm_password"
                                   class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                   required>

                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo Helper::escape($errors['confirm_password'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-outline-danger">
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thông tin tài khoản -->
            <div class="main-card card mt-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Thông tin tài khoản</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Tên đăng nhập</td>
                            <td>
                                <code><?php echo Helper::escape($currentUser['username']); ?></code>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vai trò</td>
                            <td>
                                <?php echo $currentUser['role'] === 'admin'
                                    ? '<span class="badge bg-danger">Admin</span>'
                                    : '<span class="badge bg-primary">Nhân viên</span>'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ngày tạo</td>
                            <td>
                                <?php echo Helper::formatDate($currentUser['created_at'] ?? '', 'd/m/Y'); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>