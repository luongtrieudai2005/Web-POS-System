<?php
/**
 * Bootstrap File
 * File nay se duoc include o dau moi file PHP de load tat ca class can thiet
 * 
 * QUAN TRONG: Moi file PHP trong du an deu phai require file nay dau tien!
 */

// Load cac file config
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/constants.php';

// Load cac core class theo thu tu (quan trong vi co dependency)
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Helper.php';

// Load Mailer (optional - chi can khi gui email)
// require_once __DIR__ . '/../core/Mailer.php';

// Khoi tao session
Session::start();

// Optional: Set timezone (da set trong app.php nhung co the set lai o day)
// date_default_timezone_set('Asia/Ho_Chi_Minh');
?>