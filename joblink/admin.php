<?php
require_once 'config.php';
$pageTitle = 'Panel de Administración';
require_once ROOT_PATH . 'includes/header.php';

// --- SEGURIDAD: SOLO ADMINS ---
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';

// --- CÁLCULO DE ESTADÍSTICAS ---
$total_empresas = $conn->query("SELECT COUNT(id) as total FROM usuarios WHERE tipo_usuario = 'empresa'")->fetch_assoc()['total'];
$total_candidatos = $conn->query("SELECT COUNT(id) as total FROM usuarios WHERE tipo_usuario = 'candidato'")->fetch_assoc()['total'];
$total_ofertas = $conn->query("SELECT COUNT(id) as total FROM ofertas WHERE is_active = 1 AND is_approved = 1")->fetch_assoc()['total'];

// --- OFERTAS PENDIENTES DE MODERACIÓN ---
$sql_pendientes = "SELECT o.*, u.nombre as nombre_empresa FROM ofertas o JOIN usuarios u ON o.id_empresa = u.id WHERE o.is_approved = 0 ORDER BY o.fecha_publicacion DESC LIMIT 10";
$result_pendientes = $conn->query($sql_pendientes);

?>

<div class="container page-section">
    <h2 class="section-title">Panel de Administración</h2>

    <div class="dashboard-grid">
        <nav class="dashboard-nav">
            <a href="#estadisticas" class="active">Estadísticas</a>
            <a href="#moderacion">Moderar Ofertas</a>
            <a href="/joblink/admin_usuarios.php">Gestionar Usuarios</a>
            <a href="/joblink/logout.php">Cerrar Sesión</a>
        </nav>

        <div class="dashboard-content">
            <!-- Sección de Estadísticas -->
            <section id="estadisticas" class="admin-section">
                <h3>Estadísticas Generales</h3>
                <div class="admin-stats-grid">
                    <div class="stat-card">
                        <h3><i class="fa-solid fa-building"></i> Empresas Registradas</h3>
                        <p><?php echo $total_empresas; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fa-solid fa-users"></i> Candidatos Activos</h3>
                        <p><?php echo $total_candidatos; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fa-solid fa-briefcase"></i> Ofertas Publicadas</h3>
                        <p><?php echo $total_ofertas; ?></p>
                    </div>
                </div>
            </section>

            <hr class="section-divider">

            <!-- Sección de Moderación -->
            <section id="moderacion" class="admin-section">
                <h3>Ofertas Pendientes de Aprobación</h3>
                <?php if ($result_pendientes && $result_pendientes->num_rows > 0): ?>
                    <div class="job-listings-container">
                        <?php while($oferta = $result_pendientes->fetch_assoc()): ?>
                            <div class="job-card moderation-card">
                                <div class="job-details">
                                    <h4 class="job-title"><?php echo htmlspecialchars($oferta['titulo']); ?></h4>
                                    <p class="job-company"><?php echo htmlspecialchars($oferta['nombre_empresa']); ?></p>
                                </div>
                                <div class="job-actions moderation-actions">
                                    <a href="/joblink/oferta.php?id=<?php echo $oferta['id']; ?>" target="_blank" class="btn btn-secondary">Ver</a>
                                    <form action="/joblink/php/moderar_oferta.php" method="POST" style="display:inline-flex; gap: 5px;">
                                        <input type="hidden" name="id_oferta" value="<?php echo $oferta['id']; ?>">
                                        <button type="submit" name="accion" value="aprobar" class="btn btn-success">Aprobar</button>
                                        <button type="submit" name="accion" value="rechazar" class="btn btn-danger">Rechazar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>¡Buen trabajo! No hay ofertas pendientes de moderación.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<style>
.admin-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.stat-card { background-color: var(--color-surface); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--box-shadow-sm); text-align: center; border-top: 4px solid var(--color-primary); }
.stat-card h3 { margin-bottom: 10px; color: var(--color-secondary); font-size: 1rem; }
.stat-card p { font-size: 2.5rem; font-weight: 700; color: var(--color-primary); }
.admin-section h3 { font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--color-border); }
.moderation-card { display: flex; justify-content: space-between; align-items: center; }
.moderation-actions { display: flex; gap: 10px; flex-shrink: 0; }
.btn-success { background-color: var(--color-success); color: white; }
.btn-danger { background-color: var(--color-error); color: white; }
.section-divider { margin: 40px 0; border: none; border-top: 1px solid var(--color-border); }
</style>

<?php
$conn->close();
require_once ROOT_PATH . 'includes/footer.php';
?>
