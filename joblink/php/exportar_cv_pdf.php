<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

// --- Dependencia Requerida ---
// Para que este script funcione, se debe descargar la librería FPDF
// desde http://www.fpdf.org/ y colocar el archivo fpdf.php en este mismo directorio.
if (!file_exists(__DIR__ . '/fpdf.php')) {
    die("Error: El archivo fpdf.php no se encuentra. Por favor, descargue FPDF y colóquelo en la carpeta /php/ para generar el PDF.");
}
require_once __DIR__ . '/fpdf.php';

// --- Seguridad y Conexión ---
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'candidato') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}
require_once ROOT_PATH . 'includes/conexion.php';
$id_usuario = $_SESSION['id'];

// --- Cargar todos los datos del CV ---
$stmt_user = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$stmt_exp = $conn->prepare("SELECT * FROM cv_experiencia WHERE id_usuario = ? ORDER BY fecha_inicio DESC");
$stmt_exp->bind_param("i", $id_usuario);
$stmt_exp->execute();
$exp_result = $stmt_exp->get_result();

$stmt_edu = $conn->prepare("SELECT * FROM cv_educacion WHERE id_usuario = ? ORDER BY fecha_inicio DESC");
$stmt_edu->bind_param("i", $id_usuario);
$stmt_edu->execute();
$edu_result = $stmt_edu->get_result();

$stmt_hab = $conn->prepare("SELECT * FROM cv_habilidades WHERE id_usuario = ?");
$stmt_hab->bind_param("i", $id_usuario);
$stmt_hab->execute();
$hab_result = $stmt_hab->get_result();

// --- Clase PDF Personalizada ---
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(0, 51, 102); // Azul Oscuro
        $this->Cell(0, 10, utf8_decode($GLOBALS['user']['nombre']), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(33, 37, 41); // Casi negro
        $contact_info = $GLOBALS['user']['email'] . ' | ' . $GLOBALS['user']['telefono'] . ' | ' . $GLOBALS['user']['sitio_web'];
        $this->Cell(0, 10, utf8_decode($contact_info), 0, 1, 'C');
        $this->Ln(10);
    }

    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor(248, 249, 250); // Gris claro
        $this->SetTextColor(0, 90, 158); // Azul primario
        $this->Cell(0, 8, utf8_decode($title), 0, 1, 'L', true);
        $this->Ln(4);
    }

    function SectionBody($body)
    {
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(33, 37, 41);
        $this->MultiCell(0, 6, utf8_decode($body));
        $this->Ln();
    }
}

// --- Creación del PDF ---
$pdf = new PDF();
$pdf->AddPage();

// Resumen del Perfil
$pdf->SectionTitle('Resumen Profesional');
$pdf->SectionBody($user['resumen_perfil']);

// Experiencia Laboral
$pdf->SectionTitle('Experiencia Laboral');
while($exp = $exp_result->fetch_assoc()){
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode($exp['puesto'] . ' - ' . $exp['empresa']), 0, 1);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 6, $exp['fecha_inicio'] . ' a ' . ($exp['fecha_fin'] ?? 'Presente'), 0, 1);
    $pdf->SectionBody($exp['descripcion']);
}

// Educación
$pdf->SectionTitle('Educación');
while($edu = $edu_result->fetch_assoc()){
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode($edu['titulo'] . ' - ' . $edu['institucion']), 0, 1);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 6, $edu['fecha_inicio'] . ' a ' . ($edu['fecha_fin'] ?? 'Presente'), 0, 1);
    $pdf->Ln(5);
}

// Habilidades
$pdf->SectionTitle('Habilidades');
$habilidades_str = '';
while($hab = $hab_result->fetch_assoc()){
    $habilidades_str .= $hab['habilidad'] . ', ';
}
$pdf->SectionBody(rtrim($habilidades_str, ', '));

// Salida del PDF
$pdf->Output('D', 'CV_' . str_replace(' ', '', $user['nombre']) . '.pdf');

$conn->close();
?>