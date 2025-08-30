<?php 
require_once 'config.php';
$pageTitle = 'Detalles de la Oferta'; 
require_once ROOT_PATH . 'includes/header.php'; 

if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$id_oferta = $_GET['id'];

// Añadimos is_active = 1 para asegurar que no se muestren ofertas "borradas"
$sql = "SELECT o.*, u.nombre AS nombre_empresa FROM ofertas o JOIN usuarios u ON o.id_empresa = u.id WHERE o.id = ? AND o.is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_oferta);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 1) {
    // Si no se encuentra la oferta, mostramos un mensaje amigable y salimos.
    echo "<div class='container page-section'><div class='empty-state'><p>La oferta que buscas no existe o ya no está disponible.</p></div></div>";
    require_once ROOT_PATH . 'includes/footer.php';
    exit();
}
$oferta = $result->fetch_assoc();
?>
<div class="container page-section">
    <div class="oferta-detalle-card">
        <h1><?php echo htmlspecialchars($oferta['titulo']); ?></h1>
        <p class="empresa"><?php echo htmlspecialchars($oferta['nombre_empresa']); ?></p>
        <div class="job-metadata">
            <span><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($oferta['ubicacion']); ?></span>
            <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($oferta['tipo']); ?></span>
            <span><i class="fa-solid fa-money-bill-wave"></i> <?php echo htmlspecialchars($oferta['salario']); ?></span>
        </div>
        <hr>
        <h3>Descripción del Puesto</h3>
        <p><?php echo nl2br(htmlspecialchars($oferta['descripcion'])); ?></p>
        
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['tipo_usuario'] == 'candidato'): ?>
            <form action="/joblink/php/aplicar.php" method="POST">
                <input type="hidden" name="id_oferta" value="<?php echo $oferta['id']; ?>">
                <button type="submit" class="btn btn-primary">Postularme a esta Oferta</button>
            </form>
        <?php elseif(isset($_SESSION['loggedin']) && $_SESSION['tipo_usuario'] == 'empresa'): ?>
            <p class="alert">Como empresa, no puedes postularte a ofertas.</p>
        <?php else: ?>
            <p class="alert">Debes <a href="/joblink/login.php">iniciar sesión como candidato</a> para poder postularte.</p>
        <?php endif; ?>

         <?php if(isset($_GET['status']) && $_GET['status'] == 'exitoso') { echo '<p class="alert alert-success">¡Te has postulado exitosamente!</p>'; } ?>
         <?php if(isset($_GET['error'])) { echo '<p class="alert alert-error">Error: ' . htmlspecialchars($_GET['error']) . '</p>'; } ?>
    </div>
</div>
<?php 
$stmt->close();
$conn->close();
require_once ROOT_PATH . 'includes/footer.php'; 
?>