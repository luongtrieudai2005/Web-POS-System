<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/TransactionController.php';

TransactionController::getByBarcode();
