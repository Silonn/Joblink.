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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_item'])) {
    require_once ROOT_PATH . 'includes/conexion.php';

    $id_empresa = $_SESSION['id'];
    $id_item = $_POST['id_item'];

    // Desactivar el item del portafolio (soft delete)
    $stmt_update = $conn->prepare("UPDATE empresa_portafolio SET is_active = 0 WHERE id = ? AND id_empresa = ?");
    $stmt_update->bind_param("ii", $id_item, $id_empresa);

    if ($stmt_update->execute()) {
        header('Location: ' . BASE_URL . 'dashboard.php?status=portafolio_eliminado#portafolio');
    } else {
        header('Location: ' . BASE_URL . 'dashboard.php?error=db#portafolio');
    }

    $stmt_update->close();
    $conn->close();
} else {
    header('Location: ' . BASE_URL . 'dashboard.php#portafolio');
}
?>