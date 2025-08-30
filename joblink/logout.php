<?php
// Requerimos config.php para tener disponible BASE_URL
require_once 'config.php';

// Iniciamos la sesión para poder destruirla.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Limpiamos todas las variables de sesión.
session_unset();

// Destruimos la sesión activa.
session_destroy();

// Redirigimos al usuario a la página de inicio usando la constante BASE_URL.
header("Location: " . BASE_URL . "index.php");
exit();
?>