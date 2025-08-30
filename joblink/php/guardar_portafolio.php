<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

// Seguridad: Solo para empresas logueadas
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['titulo_proyecto'])) {
    require_once ROOT_PATH . 'includes/conexion.php';

    $id_empresa = $_SESSION['id'];
    $titulo = $_POST['titulo_proyecto'];
    $descripcion = $_POST['descripcion_proyecto'];
    $imagen_path = null;

    // Lógica de subida de imagen
    if (isset($_FILES['imagen_proyecto']) && $_FILES['imagen_proyecto']['error'] == 0) {
        $target_dir = ROOT_PATH . "uploads/portfolio/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_ext = pathinfo($_FILES["imagen_proyecto"]["name"], PATHINFO_EXTENSION);
        $new_filename = "portfolio_" . $id_empresa . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        // Validar tipo de imagen
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES["imagen_proyecto"]["tmp_name"], $target_file)) {
                $imagen_path = $new_filename;
            }
        }
    }

    $sql = "INSERT INTO empresa_portafolio (id_empresa, titulo_proyecto, descripcion_proyecto, imagen_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_empresa, $titulo, $descripcion, $imagen_path);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'dashboard.php?status=portafolio_guardado#portafolio');
    } else {
        header('Location: ' . BASE_URL . 'dashboard.php?error=db#portafolio');
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ' . BASE_URL . 'dashboard.php#portafolio');
}
?>