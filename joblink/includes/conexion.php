<?php
// Incluir la configuración principal una sola vez
require_once __DIR__ . '/../config.php';

// Crear la conexión usando las constantes definidas
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Establecer el charset a utf8
$conn->set_charset("utf8");

// Verificar si hay un error de conexión
if ($conn->connect_error) {
    // En un entorno de producción, sería mejor loguear el error que mostrarlo
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>