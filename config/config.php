<?php
require_once __DIR__ . '/config.php'; // Asegurate de esto antes de usar App\Database

// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'sistema_evaluaciones');
define('DB_USER', getenv('DB_USER') ?: 'cepard_user');
define('DB_PASS', getenv('DB_PASS') ?: 'clave_segura');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Evaluaciones');
define('APP_URL', 'http://localhost/cepard');
define('APP_PATH', dirname(__DIR__));

// Configuración de sesiones
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'sistema_evaluaciones');

// Configuración de archivos
define('UPLOAD_PATH', APP_PATH . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Configuración de correo
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tu-email@gmail.com');
define('MAIL_PASSWORD', 'tu-password');
define('MAIL_FROM', 'no-reply@sistema-evaluaciones.com');
define('MAIL_FROM_NAME', 'Sistema de Evaluaciones');

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 3);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires'); 