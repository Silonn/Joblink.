<?php
require_once 'config.php';
$pageTitle = 'Mi Panel';
require_once ROOT_PATH . 'includes/header.php';

// Seguridad: Si no hay sesión, redirigir al login.
if (!isset($_SESSION['loggedin'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Redirigir al admin a su panel específico
if ($_SESSION['tipo_usuario'] == 'administrador') {
    header('Location: ' . BASE_URL . 'admin.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$id_usuario = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

?>

<div class="container page-section">
    <h2 class="section-title">Bienvenido a tu Panel, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>

    <div class="dashboard-grid">
        <nav class="dashboard-nav">
            <?php if ($tipo_usuario == 'candidato'): ?>
                <a href="#perfil" class="active">Mi Perfil y CV</a>
                <a href="#postulaciones">Mis Postulaciones</a>
            <?php elseif ($tipo_usuario == 'empresa'): ?>
                <a href="#ofertas" class="active">Mis Ofertas</a>
                <a href="/joblink/publicar-oferta.php">Publicar Nueva Oferta</a>
                <a href="#portafolio">Gestionar Portafolio</a>
            <?php endif; ?>
            <a href="/joblink/logout.php">Cerrar Sesión</a>
        </nav>

        <div class="dashboard-content">
            <?php if ($tipo_usuario == 'candidato'): ?>
                <!-- VISTA CANDIDATO -->
                <section id="perfil">
                    <h3>Tu Perfil de JobLink</h3>
                    <p>Mantén tu información actualizada para que las empresas te encuentren.</p>
                    <a href="/joblink/crear-cv.php" class="btn btn-primary">Administrar mi CV</a>
                    <hr style="margin: 20px 0;">
                    <h4>Subir CV en formato PDF</h4>
                     <form action="/joblink/php/subir_cv.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="cv">Selecciona tu CV (PDF, DOC, DOCX)</label>
                            <input type="file" name="cv" id="cv" accept=".pdf,.doc,.docx" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">Subir Archivo</button>
                    </form>
                </section>

            <?php elseif ($tipo_usuario == 'empresa'): ?>
                <!-- VISTA EMPRESA -->
                <section id="ofertas">
                    <h3>Tus Ofertas Publicadas</h3>
                    <?php 
                        $sql_ofertas = "SELECT * FROM ofertas WHERE id_empresa = ? ORDER BY fecha_publicacion DESC";
                        $stmt_ofertas = $conn->prepare($sql_ofertas);
                        $stmt_ofertas->bind_param("i", $id_usuario);
                        $stmt_ofertas->execute();
                        $result_ofertas = $stmt_ofertas->get_result();
                        if ($result_ofertas->num_rows > 0) {
                            while($oferta = $result_ofertas->fetch_assoc()) {
                                echo '<div class="job-card">';
                                echo '  <div class="job-details">
';
                                echo '      <h3 class="job-title">' . htmlspecialchars($oferta['titulo']) . '</h3>';
                                echo '      <p class="job-company">Estado: ' . ($oferta['is_active'] ? 'Activa' : 'Inactiva') . ' | Aprobada: ' . ($oferta['is_approved'] == 1 ? 'Sí' : 'Pendiente') . '</p>';
                                echo '  </div>';
                                echo '  <div class="job-actions">
';
                                echo '      <a href="' . BASE_URL . 'ver-candidatos.php?oferta_id=' . $oferta['id'] . '" class="btn btn-secondary">Ver Candidatos</a>';
                                echo '      <a href="' . BASE_URL . 'editar-oferta.php?id=' . $oferta['id'] . '" class="btn btn-primary">Editar</a>';
                                echo '  </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="empty-state"><p>Aún no has publicado ninguna oferta.</p></div>';
                        }
                    ?>
                </section>
                <hr style="margin: 40px 0;">
                <section id="portafolio">
                    <h3>Portafolio de la Empresa</h3>
                    <p>Muestra tus proyectos y logros para atraer al mejor talento.</p>
                    <!-- Formulario para añadir nuevo item al portafolio -->
                    <form action="/joblink/php/guardar_portafolio.php" method="POST" enctype="multipart/form-data" class="form-container" style="margin-bottom: 20px;">
                        <h4>Añadir Nuevo Proyecto</h4>
                        <input type="hidden" name="id_item" value="new">
                        <div class="form-group">
                            <label for="titulo_proyecto">Título del Proyecto</label>
                            <input type="text" name="titulo_proyecto" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_proyecto">Descripción</label>
                            <textarea name="descripcion_proyecto" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="imagen_proyecto">Imagen (Opcional)</label>
                            <input type="file" name="imagen_proyecto" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                    </form>

                    <!-- Lista de items del portafolio -->
                    <div class="portfolio-list">
                        <?php
                            $sql_port = "SELECT * FROM empresa_portafolio WHERE id_empresa = ? AND is_active = 1 ORDER BY id DESC";
                            $stmt_port = $conn->prepare($sql_port);
                            $stmt_port->bind_param("i", $id_usuario);
                            $stmt_port->execute();
                            $result_port = $stmt_port->get_result();
                            if($result_port->num_rows > 0) {
                                while($item = $result_port->fetch_assoc()) {
                                    echo '<div class="portfolio-item">';
                                    echo '<h5>' . htmlspecialchars($item['titulo_proyecto']) . '</h5>';
                                    echo '<p>' . htmlspecialchars($item['descripcion_proyecto']) . '</p>';
                                    echo '<form action="' . BASE_URL . 'php/eliminar_portafolio.php" method="POST" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar este proyecto?\');">
';
                                    echo '<input type="hidden" name="id_item" value="' . $item['id'] . '">';
                                    echo '<button type="submit" class="btn btn-danger">Eliminar</button>';
                                    echo '</form>';
                                    echo '</div>';
                                }
                            }
                        ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once ROOT_PATH . 'includes/footer.php';
?>
