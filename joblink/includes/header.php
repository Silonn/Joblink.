<?php 
// Iniciar la sesión solo una vez y asegurarse de que el config esté cargado.
require_once __DIR__ . '/../config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | JobLink' : 'JobLink'; ?></title>
    
    <!-- Rutas de CSS y assets usando ruta absoluta -->
    <link rel="stylesheet" href="/joblink/assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="/joblink/joblink.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <header class="main-header">
        <div class="container">
            <a href="/joblink/index.php" class="logo">
                <img src="/joblink/assets/css/images/joblink.webp" alt="Logo JobLink" style="height: 35px;">
            </a>
            <nav class="main-nav-desktop">
                <a href="/joblink/buscar.php">Ver Ofertas</a>
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['tipo_usuario'] == 'empresa'): ?>
                    <a href="/joblink/publicar-oferta.php">Publicar Oferta</a>
                <?php endif; ?>
            </nav>
            <div class="user-actions">
                <?php if(isset($_SESSION['loggedin'])): ?>
                    <a href="/joblink/dashboard.php" class="btn btn-secondary">Mi Panel</a>
                    <a href="/joblink/logout.php" class="btn btn-primary">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/joblink/login.php" class="btn btn-primary">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>