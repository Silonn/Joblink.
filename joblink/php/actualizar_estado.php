<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa' || !isset($_POST['post_id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

$post_id = $_POST['post_id'];
$nuevo_estado = $_POST['nuevo_estado'];
$oferta_id = $_POST['oferta_id'];

$estados_validos = ['Pendiente', 'Visto', 'En proceso', 'Rechazado'];
if (!in_array($nuevo_estado, $estados_validos)) {
    header('Location: ' . BASE_URL . 'ver-candidatos.php?oferta_id=' . $oferta_id . '&error=invalid_status');
    exit();
}

// Actualiza el estado en la base de datos
$sql = "UPDATE postulaciones SET estado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nuevo_estado, $post_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: ' . BASE_URL . 'ver-candidatos.php?oferta_id=' . $oferta_id . '&status=updated');
    exit();
} else {
    $stmt->close();
    $conn->close();
    header('Location: ' . BASE_URL . 'ver-candidatos.php?oferta_id=' . $oferta_id . '&error=db');
    exit();
}
?>