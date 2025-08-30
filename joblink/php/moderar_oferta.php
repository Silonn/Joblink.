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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_oferta']) && isset($_POST['accion'])) {
    require_once ROOT_PATH . 'includes/conexion.php';

    $id_oferta = $_POST['id_oferta'];
    $accion = $_POST['accion'];

    if ($accion == 'aprobar') {
        // Se aprueba la oferta, ahora será visible para todos.
        $sql = "UPDATE ofertas SET is_approved = 1, is_active = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_oferta);
    } elseif ($accion == 'rechazar') {
        // Se rechaza la oferta. No será visible y se desactiva.
        $sql = "UPDATE ofertas SET is_approved = 2, is_active = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_oferta);
    } else {
        // Si la acción no es válida, no hacemos nada.
        header('Location: ' . BASE_URL . 'admin.php?error=accion_invalida');
        exit();
    }

    if (isset($stmt) && $stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin.php?status=moderado');
    } else {
        header('Location: ' . BASE_URL . 'admin.php?error=db');
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ' . BASE_URL . 'admin.php');
}
?>