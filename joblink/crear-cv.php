<?php
require_once 'config.php';
$pageTitle = 'Creador de CV';
require_once ROOT_PATH . 'includes/header.php';

// Seguridad: Solo para candidatos logueados
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'candidato') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$id_usuario = $_SESSION['id'];

// --- Cargar datos existentes del CV ---
// Datos personales (de la tabla usuarios)
$stmt_user = $conn->prepare("SELECT nombre, email, telefono, resumen_perfil, sitio_web FROM usuarios WHERE id = ?");
if ($stmt_user === false) { die("Error al preparar la consulta de usuario: " . $conn->error); }
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();

// Experiencia
$stmt_exp = $conn->prepare("SELECT * FROM cv_experiencia WHERE id_usuario = ? AND is_active = 1 ORDER BY fecha_inicio DESC");
if ($stmt_exp === false) { die("Error al preparar la consulta de experiencia: " . $conn->error); }
$stmt_exp->bind_param("i", $id_usuario);
$stmt_exp->execute();
$exp_result = $stmt_exp->get_result();

// Educación
$stmt_edu = $conn->prepare("SELECT * FROM cv_educacion WHERE id_usuario = ? AND is_active = 1 ORDER BY fecha_inicio DESC");
if ($stmt_edu === false) { die("Error al preparar la consulta de educación: " . $conn->error); }
$stmt_edu->bind_param("i", $id_usuario);
$stmt_edu->execute();
$edu_result = $stmt_edu->get_result();

// Habilidades
$stmt_hab = $conn->prepare("SELECT * FROM cv_habilidades WHERE id_usuario = ? AND is_active = 1");
if ($stmt_hab === false) { die("Error al preparar la consulta de habilidades: " . $conn->error); }
$stmt_hab->bind_param("i", $id_usuario);
$stmt_hab->execute();
$hab_result = $stmt_hab->get_result();

?>

<div class="container page-section">
    <h2 class="section-title">Creador de CV de JobLink</h2>
    <p style="text-align:center; margin-top:-30px; margin-bottom:30px;">Completa los campos para generar un CV profesional.</p>

    <form action="/joblink/php/guardar_cv.php" method="POST" class="cv-form">
        
        <!-- === SECCIÓN: DATOS PERSONALES === -->
        <fieldset>
            <legend>Datos Personales</legend>
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email de Contacto</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user_data['telefono'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="sitio_web">Sitio Web o Portfolio</label>
                <input type="url" id="sitio_web" name="sitio_web" value="<?php echo htmlspecialchars($user_data['sitio_web'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="resumen_perfil">Resumen de tu Perfil</label>
                <textarea id="resumen_perfil" name="resumen_perfil" rows="5"><?php echo htmlspecialchars($user_data['resumen_perfil'] ?? ''); ?></textarea>
            </div>
        </fieldset>

        <!-- === SECCIÓN: EXPERIENCIA LABORAL === -->
        <fieldset>
            <legend>Experiencia Laboral</legend>
            <div id="experiencia-container">
                <!-- Las experiencias existentes se cargarán aquí -->
                <?php while($exp = $exp_result->fetch_assoc()): ?>
                <div class="form-repeater-item">
                    <input type="hidden" name="exp_id[]" value="<?php echo $exp['id']; ?>">
                    <input type="text" name="exp_puesto[]" placeholder="Puesto" value="<?php echo htmlspecialchars($exp['puesto']); ?>" required>
                    <input type="text" name="exp_empresa[]" placeholder="Empresa" value="<?php echo htmlspecialchars($exp['empresa']); ?>" required>
                    <input type="date" name="exp_inicio[]" value="<?php echo $exp['fecha_inicio']; ?>">
                    <input type="date" name="exp_fin[]" value="<?php echo $exp['fecha_fin']; ?>">
                    <textarea name="exp_desc[]" placeholder="Descripción de tareas..."><?php echo htmlspecialchars($exp['descripcion']); ?></textarea>
                    <button type="button" class="btn-remove">Eliminar</button>
                </div>
                <?php endwhile; ?>
            </div>
            <button type="button" id="add-experiencia" class="btn btn-secondary">+ Añadir Experiencia</button>
        </fieldset>

        <!-- === SECCIÓN: EDUCACIÓN === -->
        <fieldset>
            <legend>Educación</legend>
            <div id="educacion-container">
                 <?php while($edu = $edu_result->fetch_assoc()): ?>
                <div class="form-repeater-item">
                    <input type="hidden" name="edu_id[]" value="<?php echo $edu['id']; ?>">
                    <input type="text" name="edu_institucion[]" placeholder="Institución" value="<?php echo htmlspecialchars($edu['institucion']); ?>" required>
                    <input type="text" name="edu_titulo[]" placeholder="Título Obtenido" value="<?php echo htmlspecialchars($edu['titulo']); ?>" required>
                    <input type="date" name="edu_inicio[]" value="<?php echo $edu['fecha_inicio']; ?>">
                    <input type="date" name="edu_fin[]" value="<?php echo $edu['fecha_fin']; ?>">
                    <button type="button" class="btn-remove">Eliminar</button>
                </div>
                <?php endwhile; ?>
            </div>
            <button type="button" id="add-educacion" class="btn btn-secondary">+ Añadir Educación</button>
        </fieldset>

        <!-- === SECCIÓN: HABILIDADES === -->
        <fieldset>
            <legend>Habilidades</legend>
            <div id="habilidades-container">
                <p>Añade tus habilidades principales (Ej: PHP, React, Liderazgo, etc.)</p>
                <input type="text" id="habilidades-input" name="habilidades" placeholder="Escribe habilidades separadas por comas...">
                <div id="habilidades-tags">
                    <?php while($hab = $hab_result->fetch_assoc()): ?>
                        <span class="tag"><?php echo htmlspecialchars($hab['habilidad']); ?><span class="remove-tag" data-id="<?php echo $hab['id']; ?>">x</span></span>
                    <?php endwhile; ?>
                </div>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar CV</button>
            <a href="/joblink/php/exportar_cv_pdf.php" class="btn btn-accent" target="_blank">Exportar a PDF</a>
        </div>

    </form>
</div>

<style>
.cv-form fieldset { border: 1px solid var(--color-border); border-radius: var(--border-radius); padding: 20px; margin-bottom: 30px; }
.cv-form legend { font-size: 1.2rem; font-weight: 600; color: var(--color-secondary); padding: 0 10px; }
.form-repeater-item { background: #fdfdfd; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-repeater-item textarea { grid-column: 1 / -1; }
.form-actions { text-align: right; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica para añadir experiencia
    document.getElementById('add-experiencia').addEventListener('click', function() {
        const container = document.getElementById('experiencia-container');
        const newItem = document.createElement('div');
        newItem.className = 'form-repeater-item';
        newItem.innerHTML = `
            <input type="hidden" name="exp_id[]" value="new">
            <input type="text" name="exp_puesto[]" placeholder="Puesto" required>
            <input type="text" name="exp_empresa[]" placeholder="Empresa" required>
            <input type="date" name="exp_inicio[]">
            <input type="date" name="exp_fin[]">
            <textarea name="exp_desc[]" placeholder="Descripción de tareas..."></textarea>
            <button type="button" class="btn-remove">Eliminar</button>
        `;
        container.appendChild(newItem);
    });

    // Lógica para añadir educación
    document.getElementById('add-educacion').addEventListener('click', function() {
        const container = document.getElementById('educacion-container');
        const newItem = document.createElement('div');
        newItem.className = 'form-repeater-item';
        newItem.innerHTML = `
            <input type="hidden" name="edu_id[]" value="new">
            <input type="text" name="edu_institucion[]" placeholder="Institución" required>
            <input type="text" name="edu_titulo[]" placeholder="Título Obtenido" required>
            <input type="date" name="edu_inicio[]">
            <input type="date" name="edu_fin[]">
            <button type="button" class="btn-remove">Eliminar</button>
        `;
        container.appendChild(newItem);
    });

    // Lógica para eliminar items
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-remove')) {
            e.target.closest('.form-repeater-item').remove();
        }
    });
});
</script>

<?php
$stmt_user->close();
$stmt_exp->close();
$stmt_edu->close();
$stmt_hab->close();
require_once ROOT_PATH . 'includes/footer.php';
$conn->close();
?>
