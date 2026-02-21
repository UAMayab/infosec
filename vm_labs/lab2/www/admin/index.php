<?php
/**
 * Energía Marina - Panel de Administración
 *
 * VULNERABILITY: Broken Authentication (OWASP A07:2021)
 * Weak session validation - can be accessed with basic bypass
 */

session_start();

// VULNERABLE: Weak authentication check
// Session can be manipulated or bypassed
$is_admin = false;

if (isset($_SESSION['nivel_acceso']) && $_SESSION['nivel_acceso'] == 'admin') {
    $is_admin = true;
}

// VULNERABILITY: Predictable session token check
if (isset($_GET['token']) && $_GET['token'] == 'admin_access_2024') {
    $is_admin = true;
    $_SESSION['admin_bypass'] = true;
}

// VULNERABILITY: Weak cookie-based auth
if (isset($_COOKIE['admin_level']) && $_COOKIE['admin_level'] == '1') {
    $is_admin = true;
}

if (!$is_admin) {
    // Hint in error message (information disclosure)
    die('<html><head><title>Acceso Denegado</title><link rel="stylesheet" href="../css/style.css"></head><body><div class="container"><h2>⛔ Acceso Denegado</h2><p>Solo administradores pueden acceder a esta sección.</p><p><small>Hint: Verifica tus cookies, sesiones o tokens de acceso...</small></p><p><a href="../index.html">Volver al inicio</a></p></div></body></html>');
}

// If authenticated, show the flag
require_once '../config.php';
$flag_query = "SELECT flag_value FROM flags WHERE flag_name = 'FLAG_AUTH'";
$flag_result = $conn->query($flag_query);
$auth_flag = '';
if ($flag_result && $flag_result->num_rows > 0) {
    $flag_row = $flag_result->fetch_assoc();
    $auth_flag = $flag_row['flag_value'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | Energía Marina</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="matrix-bg"></div>

    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>⚡ ENERGÍA MARINA</h1>
                <p class="subtitle">Panel de Administración</p>
            </div>
            <ul class="nav-links">
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="admin-panel">
            <h2>⚙️ Panel de Administración</h2>

            <div class="alert alert-success">
                <h3>🎯 ¡Flag Capturado!</h3>
                <p><strong>FLAG BROKEN AUTHENTICATION:</strong> <code><?php echo $auth_flag; ?></code></p>
                <p><small>Has explotado exitosamente la vulnerabilidad de Autenticación Débil</small></p>
            </div>

            <div class="admin-sections">
                <div class="admin-card">
                    <h3>👥 Gestión de Usuarios</h3>
                    <p>Total de empleados registrados: 4</p>
                    <p>Usuarios activos: 3</p>
                    <button class="btn btn-secondary" disabled>Ver Usuarios</button>
                </div>

                <div class="admin-card">
                    <h3>🗄️ Base de Datos</h3>
                    <p>Estado: Conectado</p>
                    <p>Última actualización: Hoy, 10:30 AM</p>
                    <button class="btn btn-secondary" disabled>Backup DB</button>
                </div>

                <div class="admin-card">
                    <h3>📊 Logs del Sistema</h3>
                    <p>Registros hoy: 127</p>
                    <p>Eventos de seguridad: 3</p>
                    <button class="btn btn-secondary" disabled>Ver Logs</button>
                </div>

                <div class="admin-card">
                    <h3>⚙️ Configuración</h3>
                    <p>Versión del sistema: 2.4.1</p>
                    <p>Última actualización: 2024-01-10</p>
                    <button class="btn btn-secondary" disabled>Configurar</button>
                </div>
            </div>

            <div class="info-box">
                <h3>🔐 Sobre esta Vulnerabilidad</h3>
                <p>Has accedido al panel de administración explotando una debilidad en el sistema de autenticación. En un escenario real, esto permitiría:</p>
                <ul>
                    <li>Acceso a funciones administrativas</li>
                    <li>Modificación de usuarios y permisos</li>
                    <li>Acceso a datos sensibles</li>
                    <li>Control total del sistema</li>
                </ul>
                <p><strong>Mitigación:</strong> Implementar autenticación robusta, validar sesiones correctamente, no confiar en cookies o tokens predecibles.</p>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Energía Marina S.A. de C.V.</p>
        </div>
    </footer>

    <!-- Vulnerability hints -->
    <!--
    This admin panel has multiple authentication bypass vulnerabilities:

    1. GET parameter: ?token=admin_access_2024
    2. Cookie: admin_level=1
    3. Session manipulation
    4. Weak session validation

    Methods to exploit:
    - Try setting cookie: document.cookie="admin_level=1"
    - Try URL parameter: admin/index.php?token=admin_access_2024
    - Manipulate session data if you have access

    Tools: Browser DevTools, Burp Suite, OWASP ZAP, cookie editors
    -->
</body>
</html>
