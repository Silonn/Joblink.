<?php 
require_once 'config.php';
$pageTitle = 'Inicio'; 
require_once ROOT_PATH . 'includes/header.php'; 
?>

<section class="hero">
    <div class="container hero-content">
        <h1>Tu Futuro Profesional Comienza Aquí</h1>
        <p>La plataforma exclusiva que conecta el talento excepcional con oportunidades laborales de primer nivel.</p>
        
        <div class="search-container">
            <form action="/joblink/buscar.php" method="GET" class="search-form">
                <input type="text" name="q" id="live-search-input" placeholder="Buscar por puesto, empresa o tecnología..." autocomplete="off">
                <button type="submit" class="btn btn-primary">Buscar</button> 
            </form>
            <div id="search-results"></div>
        </div>
    </div>
</section>

<section id="buscar-empleos" class="page-section">
    <div class="container">
        <h2 class="section-title">Ofertas Disponibles</h2>
        <?php
        require_once ROOT_PATH . 'includes/conexion.php';
        
        // Consulta preparada para seguridad y consistencia
        $sql = "SELECT o.*, u.nombre AS nombre_empresa FROM ofertas o JOIN usuarios u ON o.id_empresa = u.id WHERE o.is_active = 1 ORDER BY o.fecha_publicacion DESC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $inicial_empresa = strtoupper(substr($row['nombre_empresa'], 0, 1));
                // Usamos BASE_URL para construir las URLs de los enlaces
                $url_oferta = BASE_URL . 'oferta.php?id=' . $row['id'];
                $url_ver_candidatos = BASE_URL . 'ver-candidatos.php?oferta_id=' . $row['id'];

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
                echo '  <div class="job-actions">';

                if (isset($_SESSION['loggedin'])) {
                    if ($_SESSION['tipo_usuario'] == 'empresa') {
                        echo '<a href="' . $url_ver_candidatos . '" class="btn btn-secondary">Ver Candidatos</a>';
                    } else {
                        echo '<a href="' . $url_oferta . '" class="btn btn-primary">Ver y Aplicar</a>';
                    }
                } else {
                    echo '<a href="' . $url_oferta . '" class="btn btn-primary">Ver y Aplicar</a>';
                }
                
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo "<div class='empty-state'><p>Actualmente no hay ofertas de empleo publicadas.</p></div>";
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>
</section>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>