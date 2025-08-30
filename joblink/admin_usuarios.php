<?php
require_once 'config.php';
$pageTitle = 'Gestionar Usuarios';
require_once ROOT_PATH . 'includes/header.php';

// --- SEGURIDAD: SOLO ADMINS ---
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';

// --- OBTENER USUARIOS ---
$sql = "SELECT id, nombre, email, tipo_usuario, is_active FROM usuarios WHERE tipo_usuario != 'administrador' ORDER BY fecha_registro DESC";
$result = $conn->query($sql);

?>

<div class="container page-section">
    <h2 class="section-title">Gestionar Usuarios</h2>
    <p style="text-align:center; margin-top:-30px; margin-bottom:30px;">Activa o desactiva cuentas de candidatos y empresas.</p>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo ucfirst($user['tipo_usuario']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <form action="/joblink/php/admin_toggle_user_status.php" method="POST">
                                    <input type="hidden" name="id_usuario" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $user['is_active']; ?>">
                                    <?php if ($user['is_active']): ?>
                                        <button type="submit" class="btn btn-danger">Desactivar</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-success">Activar</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-table-container { background-color: var(--color-surface); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--box-shadow-sm); }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--color-border); }
.admin-table thead tr { background-color: #f8f9fa; }
.admin-table th { font-weight: 600; color: var(--color-secondary); }
.status-badge { padding: 4px 8px; border-radius: 12px; font-weight: 600; font-size: 0.8rem; }
.status-badge.status-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge.status-inactive { background-color: #f8d7da; color: #842029; }
.btn-danger { padding: 5px 10px; font-size: 0.9rem; background-color: var(--color-error); color: white; }
.btn-success { padding: 5px 10px; font-size: 0.9rem; background-color: var(--color-success); color: white; }
</style>

<?php
$conn->close();
require_once ROOT_PATH . 'includes/footer.php';
?>
