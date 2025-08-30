<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

// La seguridad del candidato
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'candidato' || !isset($_POST['id_oferta'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$id_candidato = $_SESSION['id'];
$id_oferta = $_POST['id_oferta'];

//Verificar si el candidato tiene un CV (Hoja de vida.) subido
$sql_cv = "SELECT cv_path FROM usuarios WHERE id = ? AND is_active = 1";
$stmt_cv = $conn->prepare($sql_cv);
$stmt_cv->bind_param("i", $id_candidato);
$stmt_cv->execute();
$result_cv = $stmt_cv->get_result();
$user = $result_cv->fetch_assoc();

if (empty($user['cv_path'])) {
    header('Location: ' . BASE_URL . 'oferta.php?id=' . $id_oferta . '&error=Debes+subir+tu+CV+antes+de+postularte.');
    exit();
}

//Verificar si el candidato se ha postulado para una propuesta, si es asi - le dira que ya esta postulado a esta oferta.
$sql_check = "SELECT id FROM postulaciones WHERE id_oferta = ? AND id_candidato = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_oferta, $id_candidato);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    header('Location: ' . BASE_URL . 'oferta.php?id=' . $id_oferta . '&error=Ya+te+has+postulado+a+esta+oferta.');
    exit();
}

// Inserta la nueva postulacion.
$sql_insert = "INSERT INTO postulaciones (id_oferta, id_candidato) VALUES (?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ii", $id_oferta, $id_candidato);

if ($stmt_insert->execute()) {
    header('Location: ' . BASE_URL . 'oferta.php?id=' . $id_oferta . '&status=exitoso');
} else {
    header('Location: ' . BASE_URL . 'oferta.php?id=' . $id_oferta . '&error=Ocurrió+un+error+en+la+base+de+datos.');
}

$stmt_cv->close();
$stmt_check->close();
$stmt_insert->close();
$conn->close();
?>