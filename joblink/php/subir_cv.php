<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'candidato') { 
    header('Location: ' . BASE_URL . 'login.php');
    exit('Acceso denegado.'); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["cv"])) {
    // La ruta de destino ahora se construye con ROOT_PATH para ser más robusta.
    $target_dir = ROOT_PATH . "uploads/cvs/";

    if (!file_exists($target_dir)) { 
        mkdir($target_dir, 0755, true); 
    }

    $userId = $_SESSION['id'];
    $file_extension = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
    
    // Generar un nombre de archivo único para evitar colisiones.
    $new_filename = "cv_" . $userId . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Validar la extensión del archivo.
    $allowed_types = ["pdf", "doc", "docx"];
    if(!in_array(strtolower($file_extension), $allowed_types)) {
        header("Location: " . BASE_URL . "dashboard.php?error=invalidfile");
        exit();
    }

    // Mover el archivo subido al directorio de destino.
    if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
        // Actualizar la ruta del CV en la base de datos.
        $sql = "UPDATE usuarios SET cv_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        // Usamos el nombre del archivo, no la ruta completa, para guardarlo en la BD.
        $stmt->bind_param("si", $new_filename, $userId);
        
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "dashboard.php?cv=subido");
        } else {
            header("Location: " . BASE_URL . "dashboard.php?error=dbupdate");
        }
        $stmt->close();
    } else {
        header("Location: " . BASE_URL . "dashboard.php?error=uploadfailed");
    }
    $conn->close();
}
?>