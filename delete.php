<?php
require_once 'config.php';

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?mensaje=ID de anime no válido&tipo=error");
    exit();
}

$id = (int)$_GET['id'];

try {
    $pdo = conectarDB();
    
    // Verificar que el anime existe
    $stmt = $pdo->prepare("SELECT * FROM animes WHERE id = ?");
    $stmt->execute([$id]);
    $anime = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$anime) {
        header("Location: index.php?mensaje=Anime no encontrado&tipo=error");
        exit();
    }
    
    // Eliminar el anime
    $stmt = $pdo->prepare("DELETE FROM animes WHERE id = ?");
    $stmt->execute([$id]);
    
    // Verificar que se eliminó correctamente
    if ($stmt->rowCount() > 0) {
        $mensaje = "Anime '" . htmlspecialchars($anime['titulo']) . "' eliminado exitosamente";
        header("Location: index.php?mensaje=" . urlencode($mensaje) . "&tipo=success");
    } else {
        header("Location: index.php?mensaje=No se pudo eliminar el anime&tipo=error");
    }
    
} catch(PDOException $e) {
    $mensaje = "Error al eliminar el anime: " . $e->getMessage();
    header("Location: index.php?mensaje=" . urlencode($mensaje) . "&tipo=error");
}

exit();
?>
