<?php
require_once 'config.php';

// Obtener mensaje de la URL si existe
$mensaje = '';
$tipoMensaje = 'info';

if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $tipoMensaje = $_GET['tipo'] ?? 'info';
}

try {
    $pdo = conectarDB();
    
    // Obtener todos los animes
    $stmt = $pdo->query("SELECT * FROM animes ORDER BY puntuacion DESC");
    $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estadísticas
    $totalAnimes = count($animes);
    $totalEpisodios = array_sum(array_column($animes, 'episodios'));
    $puntuacionPromedio = $totalAnimes > 0 ? array_sum(array_column($animes, 'puntuacion')) / $totalAnimes : 0;
    
} catch(PDOException $e) {
    $error = "Error al obtener los animes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Anime - Sistema de Gestión</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-tv"></i> Sistema de Gestión de Anime</h1>
            <p>Administra tu colección de anime favorita</p>
        </div>
        
        <div class="content">
            <?php if (isset($error)): ?>
                <?php echo mostrarMensaje($error, 'error'); ?>
            <?php endif; ?>
            
            <?php if ($mensaje): ?>
                <?php echo mostrarMensaje($mensaje, $tipoMensaje); ?>
            <?php endif; ?>
            
            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo $totalAnimes; ?></h3>
                    <p><i class="fas fa-tv"></i> Total Animes</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalEpisodios; ?></h3>
                    <p><i class="fas fa-play-circle"></i> Total Episodios</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo number_format($puntuacionPromedio, 1); ?>/10</h3>
                    <p><i class="fas fa-star"></i> Puntuación Promedio</p>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="btn-group">
                <a href="create.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Agregar Anime
                </a>
                <a href="index.php" class="btn btn-info">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </a>
            </div>
            
            <!-- Tabla de animes -->
            <div class="table-container">
                <?php if (empty($animes)): ?>
                    <div class="empty-state">
                        <i class="fas fa-tv"></i>
                        <h3>No hay animes registrados</h3>
                        <p>Comienza agregando tu primer anime a la colección</p>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agregar Primer Anime
                        </a>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-tv"></i> Título</th>
                                <th><i class="fas fa-tags"></i> Género</th>
                                <th><i class="fas fa-play"></i> Episodios</th>
                                <th><i class="fas fa-calendar"></i> Año</th>
                                <th><i class="fas fa-star"></i> Puntuación</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($animes as $anime): ?>
                                <tr>
                                    <td><strong>#<?php echo $anime['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($anime['titulo']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($anime['genero']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">
                                            <?php echo $anime['episodios']; ?> eps
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $anime['año']; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $puntuacion = $anime['puntuacion'];
                                        $badgeClass = $puntuacion >= 8.5 ? 'badge-success' : ($puntuacion >= 7.0 ? 'badge-warning' : 'badge-danger');
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <i class="fas fa-star"></i> <?php echo $puntuacion; ?>/10
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit.php?id=<?php echo $anime['id']; ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $anime['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               title="Eliminar"
                                               onclick="return confirm('¿Estás seguro de que quieres eliminar este anime?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);
        
        // Confirmación para eliminar
        function confirmarEliminacion() {
            return confirm('¿Estás seguro de que quieres eliminar este anime? Esta acción no se puede deshacer.');
        }
    </script>
</body>
</html>
