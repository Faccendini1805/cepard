<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Auth.php';

session_start();

$auth = \App\Auth::getInstance();

if ($auth->isLoggedIn()) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor ingrese usuario y contraseña';
    } else {
        if ($auth->login($username, $password)) {
            header('Location: ' . APP_URL . '/index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}

$pageTitle = 'Iniciar Sesión';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
?> 