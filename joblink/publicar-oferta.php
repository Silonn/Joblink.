<?php 
require_once 'config.php';
$pageTitle = 'Publicar Oferta'; 
require_once ROOT_PATH . 'includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'empresa') { 
    header('Location: ' . BASE_URL . 'login.php'); 
    exit(); 
}
?>
<div class="container" style="padding-top: 40px;">
    <div class="form-container">
        <h2 style="text-align: center;">Publicar una Nueva Oferta</h2>
        <form action="/joblink/php/guardar_oferta.php" method="POST">
            <div class="form-group"><label for="titulo">Título del Puesto</label><input type="text" name="titulo" id="titulo" required></div>
            <div class="form-group"><label for="descripcion">Descripción</label><textarea name="descripcion" id="descripcion" rows="6" required></textarea></div>
            <div class="form-group"><label for="ubicacion">Ubicación</label><input type="text" name="ubicacion" id="ubicacion" required></div>
            <div class="form-group">
                <label for="tipo">Tipo de Contrato</label>
                <select name="tipo" id="tipo" required>
                    <option value="Tiempo Completo">Tiempo Completo</option>
                    <option value="Medio Tiempo">Medio Tiempo</option>
                    <option value="Por Contrato">Por Contrato</option>
                    <option value="Pasantía">Pasantía</option>
                </select>
            </div>
            <div class="form-group"><label for="salario">Salario (Opcional)</label><input type="text" name="salario" id="salario" placeholder="Ej: 25,000 al mes"></div>
            <button type="submit" class="btn btn-accent" style="width: 100%;">Publicar</button>
        </form>
    </div>
</div>
<?php require_once ROOT_PATH . 'includes/footer.php'; ?>