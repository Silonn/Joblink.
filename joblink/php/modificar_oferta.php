<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

// Seguridad: verificar que el usuario sea una empresa y esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa') {
    header('Location: ' . BASE_URL . 'login.php');
    exit('Acceso denegado.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_oferta = $_POST['id_oferta'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $tipo = $_POST['tipo'];
    $salario = !empty($_POST['salario']) ? $_POST['salario'] : 'A convenir';
    $id_empresa = $_SESSION['id'];

    // La consulta solo afectará a la oferta si el id_empresa coincide, previniendo que una empresa modifique ofertas ajenas.
    $sql = "UPDATE ofertas SET titulo = ?, descripcion = ?, ubicacion = ?, tipo = ?, salario = ? WHERE id = ? AND id_empresa = ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("sssssii", $titulo, $descripcion, $ubicacion, $tipo, $salario, $id_oferta, $id_empresa);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "dashboard.php?status=actualizado");
    } else {
        header("Location: " . BASE_URL . "editar-oferta.php?id=" . $id_oferta . "&error=db");
    }

    $stmt->close();
    $conn->close();
}
?>