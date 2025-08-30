<?php 
require_once 'config.php';
$pageTitle = 'Editar Oferta'; 
require_once ROOT_PATH . 'includes/header.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa' || !isset($_GET['id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

require_once ROOT_PATH . 'includes/conexion.php';
$id_oferta = $_GET['id'];
$id_empresa = $_SESSION['id'];

$sql = "SELECT * FROM ofertas WHERE id = ? AND id_empresa = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_oferta, $id_empresa);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows !== 1) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=noautorizado');
    exit();
}
$oferta = $result->fetch_assoc();
?>

<div class="container" style="padding-top: 40px;">
    <div class="form-container">
        <h2 style="text-align: center;">Editar Oferta de Empleo</h2>
        
        <form action="/joblink/php/modificar_oferta.php" method="POST">
            
            <input type="hidden" name="id_oferta" value="<?php echo htmlspecialchars($oferta['id']); ?>">

            <div class="form-group">
                <label for="titulo">Título del Puesto</label>
                <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($oferta['titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="6" required><?php echo htmlspecialchars($oferta['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="<?php echo htmlspecialchars($oferta['ubicacion']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Contrato</label>
                <select name="tipo" id="tipo" required>
                    <option value="Tiempo Completo" <?php echo ($oferta['tipo'] == 'Tiempo Completo') ? 'selected' : ''; ?>>Tiempo Completo</option>
                    <option value="Medio Tiempo" <?php echo ($oferta['tipo'] == 'Medio Tiempo') ? 'selected' : ''; ?>>Medio Tiempo</option>
                    <option value="Por Contrato" <?php echo ($oferta['tipo'] == 'Por Contrato') ? 'selected' : ''; ?>>Por Contrato</option>
                    <option value="Pasantía" <?php echo ($oferta['tipo'] == 'Pasantía') ? 'selected' : ''; ?>>Pasantía</option>
                </select>
            </div>
            <div class="form-group">
                <label for="salario">Salario</label>
                <input type="text" name="salario" id="salario" value="<?php echo htmlspecialchars($oferta['salario']); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php 
$stmt->close();
$conn->close();
require_once ROOT_PATH . 'includes/footer.php'; 
?>