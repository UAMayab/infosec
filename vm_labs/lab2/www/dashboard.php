<?php
/**
 * Energía Marina - Dashboard de Empleados
 *
 * VULNERABILITY: Directory Traversal / Local File Inclusion (OWASP A01:2021)
 * The 'doc' parameter is vulnerable to path traversal attacks
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

$nombre = $_SESSION['nombre'];
$username = $_SESSION['username'];
$nivel_acceso = $_SESSION['nivel_acceso'];

// VULNERABLE: File inclusion without proper validation
$documento = '';
$doc_content = '';
if (isset($_GET['doc'])) {
    $doc_file = $_GET['doc'];

    // Weak validation - easily bypassed
    if (strpos($doc_file, '..') === false) {
        // This check is insufficient and can be bypassed
    }

    // VULNERABLE: Direct file inclusion
    $filepath = "/var/www/energia-marina/docs/" . $doc_file;

    if (file_exists($filepath)) {
        $doc_content = file_get_contents($filepath);
        $documento = basename($doc_file);
    } else {
        // Try absolute path (MAJOR VULNERABILITY)
        if (file_exists($doc_file)) {
            $doc_content = file_get_contents($doc_file);
            $documento = "Sistema";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portal Empleados | Energía Marina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="matrix-bg"></div>

    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>⚡ ENERGÍA MARINA</h1>
                <p class="subtitle">Portal de Empleados</p>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Inicio</a></li>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="produccion.php">Producción</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="dashboard">
            <div class="welcome-banner">
                <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h2>
                <p>Usuario: <strong><?php echo htmlspecialchars($username); ?></strong> | Nivel: <strong><?php echo htmlspecialchars($nivel_acceso); ?></strong></p>
            </div>

            <?php if (isset($_SESSION['sql_flag'])): ?>
                <div class="alert alert-success">
                    <h3>🎯 ¡Flag Capturado!</h3>
                    <p><strong>FLAG SQL INJECTION:</strong> <code><?php echo $_SESSION['sql_flag']; ?></code></p>
                    <p><small>Has explotado exitosamente la vulnerabilidad de SQL Injection</small></p>
                </div>
            <?php endif; ?>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📊 Estadísticas Personales</h3>
                    <ul>
                        <li>Reportes Completados: 24</li>
                        <li>Horas Trabajadas: 160</li>
                        <li>Último Acceso: Hoy, 09:45 AM</li>
                    </ul>
                </div>

                <div class="dashboard-card">
                    <h3>📁 Documentos</h3>
                    <p>Accede a la documentación técnica y manuales:</p>
                    <ul>
                        <li><a href="dashboard.php?doc=manual_seguridad.txt">Manual de Seguridad</a></li>
                        <li><a href="dashboard.php?doc=procedimientos.txt">Procedimientos Operativos</a></li>
                        <li><a href="dashboard.php?doc=reportes/enero_2024.txt">Reportes Mensuales</a></li>
                    </ul>
                    <p><small class="text-muted">Tip: Usa el parámetro ?doc=archivo para visualizar documentos</small></p>
                </div>

                <div class="dashboard-card">
                    <h3>🔔 Notificaciones</h3>
                    <ul>
                        <li>⚠️ Mantenimiento programado - Plataforma Marina-3</li>
                        <li>✓ Actualización de sistema completada</li>
                        <li>📧 Nuevo comunicado de Dirección</li>
                    </ul>
                </div>

                <?php if ($nivel_acceso == 'admin'): ?>
                <div class="dashboard-card admin-card">
                    <h3>⚙️ Panel de Administrador</h3>
                    <p><a href="admin/" class="btn btn-danger">Acceder a Admin Panel</a></p>
                    <p><small>Solo accesible para nivel admin</small></p>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($documento): ?>
                <div class="document-viewer">
                    <h3>📄 Visualizando: <?php echo htmlspecialchars($documento); ?></h3>
                    <div class="doc-content">
                        <pre><?php echo htmlspecialchars($doc_content); ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Energía Marina S.A. de C.V.</p>
        </div>
    </footer>

    <!-- Developer notes -->
    <!--
    TODO: Implement proper file access controls
    TODO: Validate and sanitize 'doc' parameter
    NOTE: Current file viewer allows reading any file on the system if absolute path is provided

    Example vulnerable URLs:
    dashboard.php?doc=/etc/passwd
    dashboard.php?doc=/etc/energia-marina-secret.conf
    dashboard.php?doc=../../../etc/passwd

    The weak validation only checks for '..' in the filename, but absolute paths bypass this
    -->
</body>
</html>
