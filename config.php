<?php
// Conectar a Azure SQL Server
function conectarDB() {
    try {
        $conn = new PDO("sqlsrv:server = tcp:server-anime-sql.database.windows.net,1433; Database = bd_anime_remoto", "JosephSQL", "@Soncco-29");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        print("Error connecting to SQL Server.");
        die(print_r($e));
    }
}

// Función para limpiar datos de entrada
function limpiarDatos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para mostrar mensajes
function mostrarMensaje($mensaje, $tipo = 'info') {
    $clases = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $clase = isset($clases[$tipo]) ? $clases[$tipo] : $clases['info'];
    
    return "<div class='alert $clase alert-dismissible fade show' role='alert'>
                $mensaje
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}
?>
