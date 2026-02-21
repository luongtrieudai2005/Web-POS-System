<?php
$currentUser = Auth::user();
$activePage = $activePage ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark pos-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo Router::url('dashboard.php'); ?>">
            <?php echo APP_NAME; ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"
                       href="<?php echo Router::url('dashboard.php'); ?>">Bảng điều khiển</a>
                </li>

                <?php if (Auth::isAdmin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($activePage, ['users', 'categories', 'products']) ? 'active' : ''; ?>"
                       href="#" role="button" data-bs-toggle="dropdown">Quản lý</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item <?php echo $activePage === 'users' ? 'active' : ''; ?>"
                               href="<?php echo Router::url('users/index.php'); ?>">Nhân viên</a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo $activePage === 'categories' ? 'active' : ''; ?>"
                               href="<?php echo Router::url('categories/index.php'); ?>">Danh mục</a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo $activePage === 'products' ? 'active' : ''; ?>"
                               href="<?php echo Router::url('products/index.php'); ?>">Sản phẩm</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?php echo $activePage === 'customers' ? 'active' : ''; ?>"
                               href="<?php echo Router::url('customers/index.php'); ?>">Khách hàng</a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'products' ? 'active' : ''; ?>"
                       href="<?php echo Router::url('products/index.php'); ?>">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'customers' ? 'active' : ''; ?>"
                       href="<?php echo Router::url('customers/index.php'); ?>">Khách hàng</a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'pos' ? 'active' : ''; ?>"
                       href="<?php echo Router::url('transactions/index.php'); ?>">Bán hàng (POS)</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'reports' ? 'active' : ''; ?>"
                       href="<?php echo Router::url('reports/index.php'); ?>">Báo cáo</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                       href="#" role="button" data-bs-toggle="dropdown">
                        <?php if ($currentUser['avatar']): ?>
                            <img src="<?php echo Router::url($currentUser['avatar']); ?>"
                                 width="28" height="28"
                                 style="border-radius:50%;object-fit:cover;">
                        <?php endif; ?>
                        <span><?php echo Helper::escape($currentUser['full_name']); ?></span>
                        <?php if (Auth::isAdmin()): ?>
                            <span class="badge bg-warning text-dark">Quản trị viên</span>
                        <?php else: ?>
                            <span class="badge bg-info">Nhân viên</span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text text-muted small"><?php echo Helper::escape($currentUser['email']); ?></span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Router::url('profile/index.php'); ?>">Thông tin cá nhân</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo Router::url('logout.php'); ?>">Đăng xuất</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>