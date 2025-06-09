<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Auth.php';

session_start();

$auth = \App\Auth::getInstance();

if (!$auth->isLoggedIn()) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

$db = \App\Database::getInstance();
$user = $auth->getUser();

// Obtener estadísticas según el rol
$stats = [];

if ($auth->hasRole('Administrador')) {
    // Estadísticas para administrador
    $stats['usuarios'] = $db->query("SELECT COUNT(*) as total FROM usuarios")->fetch()['total'];
    $stats['zonas'] = $db->query("SELECT COUNT(*) as total FROM zonas")->fetch()['total'];
    $stats['centros'] = $db->query("SELECT COUNT(*) as total FROM centros")->fetch()['total'];
    $stats['tests'] = $db->query("SELECT COUNT(*) as total FROM tests")->fetch()['total'];
} elseif ($auth->hasRole('Evaluador')) {
    // Estadísticas para evaluador
    $stats['tests_activos'] = $db->query("SELECT COUNT(*) as total FROM tests WHERE activo = 1")->fetch()['total'];
    $stats['asignaciones_pendientes'] = $db->query(
        "SELECT COUNT(*) as total FROM asignaciones WHERE estado = 'pendiente'"
    )->fetch()['total'];
    $stats['asignaciones_completadas'] = $db->query(
        "SELECT COUNT(*) as total FROM asignaciones WHERE estado = 'completado'"
    )->fetch()['total'];
} else {
    // Estadísticas para usuario normal
    $stats['mis_tests_pendientes'] = $db->query(
        "SELECT COUNT(*) as total FROM asignaciones 
         WHERE persona_id = ? AND estado = 'pendiente'",
        [$user['id']]
    )->fetch()['total'];
    
    $stats['mis_tests_completados'] = $db->query(
        "SELECT COUNT(*) as total FROM asignaciones 
         WHERE persona_id = ? AND estado = 'completado'",
        [$user['id']]
    )->fetch()['total'];
}

$pageTitle = 'Dashboard';
ob_start();
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
    </div>
</div>

<div class="row g-4">
    <?php if ($auth->hasRole('Administrador')): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Usuarios</h6>
                            <h2 class="my-2"><?= $stats['usuarios'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Zonas</h6>
                            <h2 class="my-2"><?= $stats['zonas'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Centros</h6>
                            <h2 class="my-2"><?= $stats['centros'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Tests</h6>
                            <h2 class="my-2"><?= $stats['tests'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($auth->hasRole('Evaluador')): ?>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Tests Activos</h6>
                            <h2 class="my-2"><?= $stats['tests_activos'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Asignaciones Pendientes</h6>
                            <h2 class="my-2"><?= $stats['asignaciones_pendientes'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Asignaciones Completadas</h6>
                            <h2 class="my-2"><?= $stats['asignaciones_completadas'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Tests Pendientes</h6>
                            <h2 class="my-2"><?= $stats['mis_tests_pendientes'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= APP_URL ?>/tests/mis-tests" class="btn btn-primary btn-sm">
                        Ver Tests Pendientes
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Tests Completados</h6>
                            <h2 class="my-2"><?= $stats['mis_tests_completados'] ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= APP_URL ?>/tests/mis-tests?estado=completado" class="btn btn-success btn-sm">
                        Ver Historial
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($auth->hasRole('Administrador')): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Últimos Usuarios Registrados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $usuarios = $db->query(
                                    "SELECT u.username, r.nombre as rol, u.created_at 
                                     FROM usuarios u 
                                     JOIN roles r ON u.rol_id = r.id 
                                     ORDER BY u.created_at DESC 
                                     LIMIT 5"
                                )->fetchAll();
                                
                                foreach ($usuarios as $usuario):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['username']) ?></td>
                                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i>Últimos Tests Creados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Test</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tests = $db->query(
                                    "SELECT nombre, activo, created_at 
                                     FROM tests 
                                     ORDER BY created_at DESC 
                                     LIMIT 5"
                                )->fetchAll();
                                
                                foreach ($tests as $test):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($test['nombre']) ?></td>
                                    <td>
                                        <?php if ($test['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($test['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
?> 