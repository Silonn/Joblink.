<?php 
require_once 'config.php';
$pageTitle = 'Iniciar Sesión'; 
require_once ROOT_PATH . 'includes/header.php'; 
?>
<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-logo">
            <a href="/joblink/index.php"><img src="/joblink/assets/css/images/joblink.webp" alt="JobLink Logo"></a>
        </div>
        <h2>Acceso a la Plataforma</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Ingresa tus credenciales para continuar.</p>

        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exitoso') { echo '<p class="alert alert-success">¡Registro exitoso! Ya puedes iniciar sesión.</p>'; } ?>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'credenciales') { echo '<p class="alert alert-error">El correo o la contraseña son incorrectos.</p>'; } ?>
        
        <form action="/joblink/php/iniciar_sesion.php" method="POST">
            <div class="form-group">
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="Correo Electrónico" required>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Acceder</button>
        </form>
        <p class="auth-footer-link">¿No tienes cuenta? <a href="/joblink/registro.php">Regístrate</a></p>
    </div>
</div>
<?php 
require_once ROOT_PATH . 'includes/footer.php'; 
?>