<?php
/**
 * Constants
 * Cac hang so bo sung cho ung dung
 */

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_SALESPERSON', 'salesperson');

// User Status
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_LOCKED', 'locked');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Date Formats
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');
define('TIME_FORMAT', 'H:i');

// Messages
define('MSG_SUCCESS', 'Thao tac thanh cong!');
define('MSG_ERROR', 'Co loi xay ra. Vui long thu lai!');
define('MSG_UNAUTHORIZED', 'Ban khong co quyen truy cap!');
define('MSG_NOT_FOUND', 'Khong tim thay du lieu!');
define('MSG_INVALID_INPUT', 'Du lieu nhap vao khong hop le!');

// Order Status (se dung sau)
define('ORDER_STATUS_COMPLETED', 'completed');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Payment Methods (se dung sau)
define('PAYMENT_CASH', 'cash');
define('PAYMENT_CARD', 'card');
define('PAYMENT_TRANSFER', 'transfer');
?>