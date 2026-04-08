<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
Auth::logout();
header('Location: ' . BASE_URL . '/pages/login.php');
exit;