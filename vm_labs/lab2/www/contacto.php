<?php
/**
 * Energía Marina - Formulario de Contacto
 *
 * VULNERABILITY: Stored Cross-Site Scripting (XSS) (OWASP A03:2021)
 * User input is not sanitized and is stored/displayed without encoding
 */

require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // VULNERABLE: No input sanitization or validation
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];

    // Insert without sanitization (XSS vulnerability)
    $query = "INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES ('$nombre', '$email', '$asunto', '$mensaje')";

    if ($conn->query($query)) {
        $success = 'Mensaje enviado correctamente. Nuestro equipo se pondrá en contacto pronto.';

        // If XSS payload is detected in the message, grant XSS flag (for educational purposes)
        if (stripos($mensaje, '<script>') !== false || stripos($mensaje, 'alert') !== false || stripos($mensaje, 'onerror') !== false) {
            $flag_query = "SELECT flag_value FROM flags WHERE flag_name = 'FLAG_XSS'";
            $flag_result = $conn->query($flag_query);
            if ($flag_result && $flag_result->num_rows > 0) {
                $flag_row = $flag_result->fetch_assoc();
                $xss_flag = $flag_row['flag_value'];
                $success .= '<br><br><strong>🎯 ¡Flag Capturado!</strong><br><code>' . $xss_flag . '</code><br><small>Has explotado exitosamente la vulnerabilidad de XSS</small>';
            }
        }
    } else {
        $error = 'Error al enviar el mensaje. Intente nuevamente.';
    }
}

// Fetch recent messages (VULNERABLE: XSS when displaying)
$messages_query = "SELECT * FROM mensajes ORDER BY fecha DESC LIMIT 5";
$messages_result = $conn->query($messages_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Energía Marina</title>
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
                <li><a href="login.php">Portal Empleados</a></li>
                <li><a href="produccion.php">Producción</a></li>
                <li><a href="contacto.php" class="active">Contacto</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="contact-section">
            <h2>📧 Contáctanos</h2>
            <p>¿Tienes preguntas sobre nuestros servicios? Envíanos un mensaje y te responderemos en menos de 24 horas.</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✓ <?php echo $success; // VULNERABLE: Unescaped output ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="contacto.php" class="contact-form">
                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Juan Pérez">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" required placeholder="Consulta sobre servicios">
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="6" required placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
            </form>

            <div class="contact-info">
                <h3>📍 Información de Contacto</h3>
                <p><strong>Oficinas Centrales:</strong><br>
                Av. Miguel Alemán #2500, Col. Centro<br>
                Veracruz, Ver., C.P. 91700, México</p>
                <p><strong>Teléfono:</strong> +52 (229) 931-5000</p>
                <p><strong>Email:</strong> contacto@energiamarina.mx</p>
                <p><strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <div class="recent-messages">
            <h3>💬 Mensajes Recientes</h3>
            <p><small>Últimos mensajes enviados por nuestros visitantes:</small></p>

            <?php if ($messages_result && $messages_result->num_rows > 0): ?>
                <?php while($msg = $messages_result->fetch_assoc()): ?>
                    <div class="message-card">
                        <p><strong><?php echo $msg['nombre']; // VULNERABLE: No XSS protection ?></strong> - <?php echo $msg['email']; ?></p>
                        <p><em><?php echo $msg['asunto']; // VULNERABLE: No XSS protection ?></em></p>
                        <p><?php echo $msg['mensaje']; // VULNERABLE: Stored XSS here ?></p>
                        <p><small><?php echo $msg['fecha']; ?></small></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay mensajes recientes.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Energía Marina S.A. de C.V. | Veracruz, México</p>
        </div>
    </footer>

    <!-- Security flaw notes -->
    <!--
    VULNERABILITY: This form is vulnerable to Stored XSS attacks
    - User input is not sanitized before storage
    - Output is not encoded when displayed
    - Recent messages section displays unescaped HTML

    Example XSS payloads to test:
    <script>alert('XSS')</script>
    <img src=x onerror=alert('XSS')>
    <svg onload=alert('XSS')>

    The flag will be revealed when XSS payload is successfully submitted
    -->
</body>
</html>
