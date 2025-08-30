<?php 
require_once 'config.php';
$pageTitle = 'Resultados de Búsqueda'; 
require_once ROOT_PATH . 'includes/header.php'; 

// Verificamos si se ha enviado un término de búsqueda. Si no, redirigimos al inicio.
if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$query = trim($_GET['q']);

// Preparamos el término de búsqueda para la consulta SQL LIKE
$search_term = "%" . $query . "%";
?>

<div class="page-section">
    <div class="container">
        <h2 class="section-title">Resultados de búsqueda para: "<?php echo htmlspecialchars($query); ?>"</h2>
        
        <div class="job-listings-container">
            <?php
            // Preparamos una consulta segura para buscar en el título, descripción y nombre de la empresa
            $sql = "SELECT o.*, u.nombre AS nombre_empresa FROM ofertas o JOIN usuarios u ON o.id_empresa = u.id WHERE (o.titulo LIKE ? OR o.descripcion LIKE ? OR u.nombre LIKE ?) AND o.is_active = 1 ORDER BY o.fecha_publicacion DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $search_term, $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $url_oferta = BASE_URL . 'oferta.php?id=' . $row['id'];
                    $inicial_empresa = strtoupper(substr($row['nombre_empresa'], 0, 1));
                    echo '<div class="job-card">';
                    echo '  <div class="job-logo">' . htmlspecialchars($inicial_empresa) . '</div>';
                    echo '  <div class="job-details">';
                    echo '      <h3 class="job-title"><a href="' . $url_oferta . '">' . htmlspecialchars($row['titulo']) . '</a></h3>';
                    echo '      <p class="job-company">' . htmlspecialchars($row['nombre_empresa']) . '</p>';
                    echo '      <div class="job-metadata">';
                    echo '          <span><i class="fa-solid fa-location-dot"></i> ' . htmlspecialchars($row['ubicacion']) . '</span>';
                    echo '          <span><i class="fa-solid fa-briefcase"></i> ' . htmlspecialchars($row['tipo']) . '</span>';
                    echo '          <span><i class="fa-solid fa-money-bill-wave"></i> ' . htmlspecialchars($row['salario']) . '</span>';
                    echo '      </div>';
                    echo '  </div>';
                    echo '  <div class="job-actions"><a href="' . $url_oferta . '" class="btn btn-primary">Ver y Aplicar</a></div>';
                    echo '</div>';
                }
            } else {
                echo "<div class='empty-state'><p>No se encontraron ofertas que coincidan con tu búsqueda.</p><p>Intenta con otros términos o revisa las ofertas más recientes en la página de inicio.</p></div>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>