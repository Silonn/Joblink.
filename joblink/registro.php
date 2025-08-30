<?php 
require_once 'config.php';
$pageTitle = 'Registro'; 
require_once ROOT_PATH . 'includes/header.php'; 
?>
<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-logo">
            <a href="/joblink/index.php"><img src="/joblink/assets/css/images/joblink.webp" alt="JobLink Logo"></a>
        </div>
        <h2>Crea tu Cuenta</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Únete a la plataforma líder para profesionales.</p>

        <?php if(isset($_GET['error'])) { echo '<p class="alert alert-error">Hubo un error en tu registro. Por favor, intenta de nuevo.</p>'; } ?>

        <form action="/joblink/php/registrar.php" method="POST">
            <div class="form-group">
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nombre" placeholder="Nombre Completo o Empresa" required>
                </div>
            </div>
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
            <div class="form-group">
                <div class="input-group">
                    <i class="fa-solid fa-briefcase"></i>
                    <select name="tipo_usuario" required>
                        <option value="" disabled selected>-- Selecciona tipo de cuenta --</option>
                        <option value="candidato">Soy Candidato</option>
                        <option value="empresa">Soy una Empresa</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>
        <p class="auth-footer-link">¿Ya tienes cuenta? <a href="/joblink/login.php">Inicia Sesión</a></p>
    </div>
</div>
<?php require_once ROOT_PATH . 'includes/footer.php'; ?>