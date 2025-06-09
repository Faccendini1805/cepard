<?php
namespace App;

require_once __DIR__ . '/Database.php';

class Auth {
    private static $instance = null;
    private $db;
    private $user = null;

    private function __construct() {
        $this->db = Database::getInstance();
        $this->checkSession();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login($username, $password) {
        $stmt = $this->db->query(
            "SELECT u.*, r.nombre as rol_nombre 
             FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id 
             WHERE u.username = ? AND u.activo = 1",
            [$username]
        );
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $this->startSession($user);
            return true;
        }
        
        return false;
    }

    public function logout() {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
        $this->user = null;
    }

    private function startSession($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['rol_id'] = $user['rol_id'];
        $_SESSION['rol_nombre'] = $user['rol_nombre'];
        $_SESSION['last_activity'] = time();
        $this->user = $user;
    }

    private function checkSession() {
        if (isset($_SESSION['user_id'])) {
            if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
                $this->logout();
                return false;
            }
            
            $stmt = $this->db->query(
                "SELECT u.*, r.nombre as rol_nombre 
                 FROM usuarios u 
                 JOIN roles r ON u.rol_id = r.id 
                 WHERE u.id = ? AND u.activo = 1",
                [$_SESSION['user_id']]
            );
            
            $this->user = $stmt->fetch();
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }

    public function isLoggedIn() {
        return $this->user !== null;
    }

    public function getUser() {
        return $this->user;
    }

    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return $this->user['rol_nombre'] === $role;
    }

    public function requireRole($role) {
        if (!$this->hasRole($role)) {
            header('Location: ' . APP_URL . '/login.php?error=unauthorized');
            exit;
        }
    }

    public function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            throw new \Exception('CSRF token validation failed');
        }
        return true;
    }

    private function __clone() {}
    public function __wakeup() {}
} 