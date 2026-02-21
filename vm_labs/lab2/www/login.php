<?php
/**
 * Energía Marina - Portal de Empleados
 * Sistema de Autenticación
 *
 * VULNERABILITY: SQL Injection (OWASP A03:2021)
 * This login form is vulnerable to SQL injection attacks
 */

require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // VULNERABLE: Direct SQL query without sanitization or prepared statements
    $query = "SELECT * FROM empleados WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['nivel_acceso'] = $user['nivel_acceso'];
        $_SESSION['user_id'] = $user['id'];

        // Check if user is admin for flag
        if ($user['nivel_acceso'] == 'admin') {
            // Query to get SQL injection flag
            $flag_query = "SELECT flag_value FROM flags WHERE flag_name = 'FLAG_SQL'";
            $flag_result = $conn->query($flag_query);
            if ($flag_result && $flag_result->num_rows > 0) {
                $flag_row = $flag_result->fetch_assoc();
                $_SESSION['sql_flag'] = $flag_row['flag_value'];
            }
        }

        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Empleados | Energía Marina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="matrix-bg"></div>

    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>⚡ ENERGÍA MARINA</h1>
                <p class="subtitle">Extracción Petrolera | Golfo de México</p>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Inicio</a></li>
                <li><a href="login.php" class="active">Portal Empleados</a></li>
                <li><a href="produccion.php">Producción</a></li>
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="login-container">
            <div class="login-box">
                <h2>🔐 Portal de Empleados</h2>
                <p class="login-subtitle">Acceso Restringido - Solo Personal Autorizado</p>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        ⚠️ <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        ✓ <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username" required placeholder="Ingrese su usuario">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
                    </div>

                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>

                <div class="login-help">
                    <p><small>¿Olvidaste tu contraseña? Contacta a soporte técnico</small></p>
                    <p><small>Usuarios registrados: <code>admin, jperez, mrodriguez, lgarcia</code></small></p>
                </div>
            </div>

            <div class="security-notice">
                <h3>🛡️ Aviso de Seguridad</h3>
                <p>Este portal utiliza conexión segura. Todos los accesos son registrados y monitoreados.</p>
                <p><small>Si detecta actividad sospechosa, repórtela inmediatamente a: <strong>seguridad@energiamarina.mx</strong></small></p>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Energía Marina S.A. de C.V. | Veracruz, México</p>
        </div>
    </footer>

    <!-- Debugging info (should be removed in production) -->
    <!--
    Common test credentials:
    - admin / admin123
    - jperez / veracruz2024

    SQL Query being used: SELECT * FROM empleados WHERE username = '$username' AND password = '$password'

    Hint for penetration testers: Try SQL injection techniques like:
    ' OR '1'='1
    admin'--
    -->
</body>
</html>
