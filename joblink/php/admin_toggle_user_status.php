<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

// --- SEGURIDAD: SOLO ADMINS ---
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header('Location: ' . BASE_URL . 'index.php');
    exit('Acceso denegado.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario']) && isset($_POST['current_status'])) {
    require_once ROOT_PATH . 'includes/conexion.php';

    $id_usuario = $_POST['id_usuario'];
    $current_status = $_POST['current_status'];

    // Determinar el nuevo estado (invertir el actual)
    $new_status = $current_status == 1 ? 0 : 1;

    $sql = "UPDATE usuarios SET is_active = ? WHERE id = ? AND tipo_usuario != 'administrador'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $id_usuario);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin_usuarios.php?status=updated');
        exit();
    } else {
        header('Location: ' . BASE_URL . 'admin_usuarios.php?error=db');
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ' . BASE_URL . 'admin_usuarios.php');
    exit();
}
?>