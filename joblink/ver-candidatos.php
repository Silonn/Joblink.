<?php 
require_once 'config.php';
$pageTitle = 'Ver Candidatos'; 
require_once ROOT_PATH . 'includes/header.php'; 

// --- SEGURIDAD ---
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa' || !isset($_GET['oferta_id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$id_oferta = $_GET['oferta_id'];
$id_empresa = $_SESSION['id'];

// --- VERIFICACIÓN ---
$sql_oferta = "SELECT titulo FROM ofertas WHERE id = ? AND id_empresa = ? AND is_active = 1";
$stmt_oferta = $conn->prepare($sql_oferta);
$stmt_oferta->bind_param("ii", $id_oferta, $id_empresa);
$stmt_oferta->execute();
$result_oferta = $stmt_oferta->get_result();

if($result_oferta->num_rows !== 1) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=noautorizado'); 
    exit();
}
$oferta = $result_oferta->fetch_assoc();
?>

<div class="container page-section">
    <div class="section-header">
        <h2 class="section-title" style="text-align: left; margin-bottom: 0;">Candidatos para: "<?php echo htmlspecialchars($oferta['titulo']); ?>"</h2>
        <a href="/joblink/dashboard.php" class="btn btn-secondary">Volver a mis ofertas</a>
    </div>
    
    <div class="candidatos-lista" style="margin-top: 30px;">
        <?php
        // --- CONSULTA PRINCIPAL ---
        $sql_candidatos = "SELECT u.nombre, u.email, u.cv_path, p.id AS post_id, p.estado 
                           FROM usuarios u 
                           JOIN postulaciones p ON u.id = p.id_candidato 
                           WHERE p.id_oferta = ? AND u.is_active = 1";
        $stmt_candidatos = $conn->prepare($sql_candidatos);
        $stmt_candidatos->bind_param("i", $id_oferta);
        $stmt_candidatos->execute();
        $result_candidatos = $stmt_candidatos->get_result();

        if ($result_candidatos->num_rows > 0) {
            while ($candidato = $result_candidatos->fetch_assoc()) {
                $cv_path = !empty($candidato['cv_path']) ? BASE_URL . 'uploads/cvs/' . htmlspecialchars($candidato['cv_path']) : '#';

                echo '<div class="candidato-card">';
                echo '  <div class="candidato-info">';
                echo '      <h4>' . htmlspecialchars($candidato['nombre']) . '</h4>';
                echo '      <p class="text-secondary">' . htmlspecialchars($candidato['email']) . '</p>';
                if(!empty($candidato['cv_path'])){
                    echo '  <a href="' . $cv_path . '" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-file-arrow-down"></i> Ver CV</a>';
                } else {
                    echo '  <p class="text-secondary"><em>El candidato no ha subido un CV.</em></p>';
                }
                echo '  </div>';
                echo '  <div class="candidato-estado">';
                echo '      <p>Estado actual: <strong>' . htmlspecialchars($candidato['estado']) . '</strong></p>';
                echo '      <form action="' . BASE_URL . 'php/actualizar_estado.php" method="POST" class="estado-form">';
                echo '          <input type="hidden" name="post_id" value="' . $candidato['post_id'] . '">';
                echo '          <input type="hidden" name="oferta_id" value="' . $id_oferta . '">';
                echo '          <select name="nuevo_estado" class="form-control">';
                echo '              <option value="Pendiente" ' . ($candidato['estado'] == 'Pendiente' ? 'selected' : '') . '>Pendiente</option>';
                echo '              <option value="Visto" ' . ($candidato['estado'] == 'Visto' ? 'selected' : '') . '>Visto</option>';
                echo '              <option value="En proceso" ' . ($candidato['estado'] == 'En proceso' ? 'selected' : '') . '>En proceso</option>';
                echo '              <option value="Rechazado" ' . ($candidato['estado'] == 'Rechazado' ? 'selected' : '') . '>Rechazado</option>';
                echo '          </select>';
                echo '          <button type="submit" class="btn btn-primary">Actualizar</button>';
                echo '      </form>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<div class="empty-state"><p>Aún no hay candidatos postulados para esta oferta.</p></div>';
        }
        $stmt_oferta->close();
        $stmt_candidatos->close();
        $conn->close();
        ?>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>