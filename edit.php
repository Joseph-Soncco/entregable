<?php
require_once 'config.php';

$errores = [];
$valores = [
    'titulo' => '',
    'genero' => '',
    'episodios' => '',
    'año' => '',
    'puntuacion' => ''
];

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?mensaje=ID de anime no válido&tipo=error");
    exit();
}

$id = (int)$_GET['id'];

try {
    $pdo = conectarDB();
    
    // Obtener el anime actual
    $stmt = $pdo->prepare("SELECT * FROM animes WHERE id = ?");
    $stmt->execute([$id]);
    $anime = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$anime) {
        header("Location: index.php?mensaje=Anime no encontrado&tipo=error");
        exit();
    }
    
    // Cargar valores actuales
    $valores = [
        'titulo' => $anime['titulo'],
        'genero' => $anime['genero'],
        'episodios' => $anime['episodios'],
        'año' => $anime['año'],
        'puntuacion' => $anime['puntuacion']
    ];
    
} catch(PDOException $e) {
    header("Location: index.php?mensaje=Error al cargar el anime&tipo=error");
    exit();
}

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y limpiar datos
    $titulo = limpiarDatos($_POST['titulo'] ?? '');
    $genero = limpiarDatos($_POST['genero'] ?? '');
    $episodios = limpiarDatos($_POST['episodios'] ?? '');
    $año = limpiarDatos($_POST['año'] ?? '');
    $puntuacion = limpiarDatos($_POST['puntuacion'] ?? '');
    
    // Validaciones
    if (empty($titulo)) {
        $errores['titulo'] = 'El título es obligatorio';
    }
    
    if (empty($genero)) {
        $errores['genero'] = 'El género es obligatorio';
    }
    
    if (empty($episodios)) {
        $errores['episodios'] = 'El número de episodios es obligatorio';
    } elseif (!is_numeric($episodios) || $episodios <= 0) {
        $errores['episodios'] = 'Los episodios deben ser un número mayor a 0';
    }
    
    if (empty($año)) {
        $errores['año'] = 'El año es obligatorio';
    } elseif (!is_numeric($año) || $año < 1900 || $año > date('Y') + 1) {
        $errores['año'] = 'El año debe ser válido';
    }
    
    if (!empty($puntuacion) && (!is_numeric($puntuacion) || $puntuacion < 0 || $puntuacion > 10)) {
        $errores['puntuacion'] = 'La puntuación debe ser un número entre 0 y 10';
    }
    
    // Guardar valores para mostrar en caso de error
    $valores = [
        'titulo' => $titulo,
        'genero' => $genero,
        'episodios' => $episodios,
        'año' => $año,
        'puntuacion' => $puntuacion
    ];
    
    // Si no hay errores, actualizar en la base de datos
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("UPDATE animes SET titulo = ?, genero = ?, episodios = ?, año = ?, puntuacion = ? WHERE id = ?");
            $stmt->execute([$titulo, $genero, $episodios, $año, $puntuacion, $id]);
            
            // Redirigir con mensaje de éxito
            header("Location: index.php?mensaje=Anime actualizado exitosamente&tipo=success");
            exit();
            
        } catch(PDOException $e) {
            $errores['general'] = "Error al actualizar el anime: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anime - CRUD Anime</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Editar Anime</h1>
            <p>Modifica la información del anime #<?php echo $id; ?></p>
        </div>
        
        <div class="content">
            <!-- Navegación -->
            <div class="btn-group">
                <a href="index.php" class="btn btn-info">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
                <a href="create.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Agregar Nuevo
                </a>
            </div>
            
            <!-- Mostrar errores generales -->
            <?php if (isset($errores['general'])): ?>
                <?php echo mostrarMensaje($errores['general'], 'error'); ?>
            <?php endif; ?>
            
            <!-- Información del anime -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Anime ID:</strong> #<?php echo $anime['id']; ?>
            </div>
            
            <!-- Formulario -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $id; ?>" novalidate>
                    <div class="form-group">
                        <label for="titulo" class="form-label">
                            <i class="fas fa-tv"></i> Título del Anime *
                        </label>
                        <input type="text" 
                               id="titulo" 
                               name="titulo" 
                               class="form-control <?php echo isset($errores['titulo']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($valores['titulo']); ?>"
                               placeholder="Ej: Attack on Titan"
                               required>
                        <?php if (isset($errores['titulo'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['titulo']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="genero" class="form-label">
                            <i class="fas fa-tags"></i> Género *
                        </label>
                        <input type="text" 
                               id="genero" 
                               name="genero" 
                               class="form-control <?php echo isset($errores['genero']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($valores['genero']); ?>"
                               placeholder="Ej: Acción, Aventura, Comedia"
                               required>
                        <?php if (isset($errores['genero'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['genero']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="episodios" class="form-label">
                            <i class="fas fa-play"></i> Número de Episodios *
                        </label>
                        <input type="number" 
                               id="episodios" 
                               name="episodios" 
                               class="form-control <?php echo isset($errores['episodios']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($valores['episodios']); ?>"
                               placeholder="24"
                               min="1"
                               required>
                        <?php if (isset($errores['episodios'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['episodios']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="año" class="form-label">
                            <i class="fas fa-calendar"></i> Año de Estreno *
                        </label>
                        <input type="number" 
                               id="año" 
                               name="año" 
                               class="form-control <?php echo isset($errores['año']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($valores['año']); ?>"
                               placeholder="2023"
                               min="1900"
                               max="<?php echo date('Y') + 1; ?>"
                               required>
                        <?php if (isset($errores['año'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['año']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="puntuacion" class="form-label">
                            <i class="fas fa-star"></i> Puntuación (0-10)
                        </label>
                        <input type="number" 
                               id="puntuacion" 
                               name="puntuacion" 
                               class="form-control <?php echo isset($errores['puntuacion']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($valores['puntuacion']); ?>"
                               placeholder="8.5"
                               step="0.1"
                               min="0"
                               max="10">
                        <?php if (isset($errores['puntuacion'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['puntuacion']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Actualizar Anime
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <a href="delete.php?id=<?php echo $id; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('¿Estás seguro de que quieres eliminar este anime?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                    }
                });
            });
            
            // Validación de episodios
            const episodiosInput = document.getElementById('episodios');
            episodiosInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (isNaN(value) || value <= 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            // Validación del año
            const añoInput = document.getElementById('año');
            añoInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                const currentYear = new Date().getFullYear();
                if (isNaN(value) || value < 1900 || value > currentYear + 1) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            // Validación de puntuación
            const puntuacionInput = document.getElementById('puntuacion');
            puntuacionInput.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (this.value !== '' && (isNaN(value) || value < 0 || value > 10)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
