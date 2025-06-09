<?php
require_once __DIR__ . '/../config/config.php';
$auth = \App\Auth::getInstance();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #dc3545;
            --light-color: #f8f9fa;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
        }
        
        .navbar-brand, .nav-link {
            color: white !important;
        }
        
        .nav-link:hover {
            color: var(--light-color) !important;
            opacity: 0.8;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-danger {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .footer {
            margin-top: auto;
            background-color: var(--light-color);
            padding: 1rem 0;
        }
        
        .table thead {
            background-color: var(--primary-color);
            color: white;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>">
                <i class="fas fa-clipboard-check me-2"></i><?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if ($auth->isLoggedIn()): ?>
                    <ul class="navbar-nav me-auto">
                        <?php if ($auth->hasRole('Administrador')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cogs me-1"></i>Administración
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/usuarios">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/zonas">Zonas</a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/centros">Centros</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($auth->hasRole('Administrador') || $auth->hasRole('Evaluador')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tasks me-1"></i>Tests
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/tests">Gestionar Tests</a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/asignaciones">Asignaciones</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/tests/mis-tests">
                                <i class="fas fa-clipboard-list me-1"></i>Mis Tests
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= htmlspecialchars($auth->getUser()['username']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/perfil">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/login.php">Iniciar Sesión</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mb-4">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <?php if (isset($content)) echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <span class="text-muted">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</span>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Inicializar DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: <?= ITEMS_PER_PAGE ?>,
                responsive: true
            });
        });
    </script>
    
    <?php if (isset($scripts)) echo $scripts; ?>
</body>
</html> 