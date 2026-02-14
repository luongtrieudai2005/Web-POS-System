<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
UserController::detail($id);

?>