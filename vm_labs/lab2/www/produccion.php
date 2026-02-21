<?php
/**
 * Energía Marina - Dashboard de Producción
 * Datos públicos de producción petrolera
 */

require_once 'config.php';

// Fetch production data
$query = "SELECT * FROM produccion ORDER BY fecha DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción | Energía Marina</title>
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
                <li><a href="produccion.php" class="active">Producción</a></li>
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="production-section">
            <h2>📊 Dashboard de Producción</h2>
            <p>Datos de producción diaria de nuestras plataformas petroleras en el Golfo de México</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>45,200</h3>
                    <p>Barriles/Día (Promedio)</p>
                    <span class="trend-up">↑ 3.2%</span>
                </div>
                <div class="stat-card">
                    <h3>12</h3>
                    <p>Plataformas Activas</p>
                    <span class="trend-stable">→ Estable</span>
                </div>
                <div class="stat-card">
                    <h3>98.5%</h3>
                    <p>Eficiencia Operativa</p>
                    <span class="trend-up">↑ 1.8%</span>
                </div>
            </div>

            <div class="production-table">
                <h3>🛢️ Producción por Plataforma</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Plataforma</th>
                            <th>Barriles Diarios</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($row['plataforma']); ?></td>
                                    <td><?php echo number_format($row['barriles_diarios']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['estado']); ?>">
                                            <?php echo htmlspecialchars($row['estado']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay datos disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <h3>ℹ️ Información Adicional</h3>
                <p>Los datos mostrados representan la producción diaria actualizada. Para acceso a reportes detallados y análisis históricos, inicia sesión en el <a href="login.php">Portal de Empleados</a>.</p>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Energía Marina S.A. de C.V. | Veracruz, México</p>
            <p><small><a href="info.php">System Info</a> | <a href="admin/">Admin</a></small></p>
        </div>
    </footer>
</body>
</html>
