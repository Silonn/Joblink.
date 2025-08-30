<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa') {
    // Redirigir o mostrar un error si no es una empresa logueada
    header('Location: ' . BASE_URL . 'login.php');
    exit('Acceso denegado.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $tipo = $_POST['tipo'];
    $salario = !empty($_POST['salario']) ? $_POST['salario'] : 'A convenir';
    $id_empresa = $_SESSION['id'];
    
    // Por defecto, las nuevas ofertas se crean como activas (is_active = 1)
    $sql = "INSERT INTO ofertas (id_empresa, titulo, descripcion, ubicacion, tipo, salario, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("isssss", $id_empresa, $titulo, $descripcion, $ubicacion, $tipo, $salario);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "dashboard.php?oferta=creada");
    } else {
        header("Location: " . BASE_URL . "publicar-oferta.php?error=dberror");
    }
    $stmt->close();
    $conn->close();
}
?>