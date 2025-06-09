<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Auth.php';

session_start();

$auth = \App\Auth::getInstance();
$auth->logout();

$_SESSION['flash_message'] = 'Has cerrado sesión correctamente.';
$_SESSION['flash_type'] = 'info';

header('Location: ' . APP_URL . '/login.php');
exit; 