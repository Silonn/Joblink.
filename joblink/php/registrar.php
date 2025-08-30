<?php
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];

    // Validación básica en el servidor
    if (empty($nombre) || empty($email) || empty($password) || empty($tipo_usuario)) {
        header("Location: " . BASE_URL . "registro.php?error=camposvacios");
        exit();
    }

    // Hashear la contraseña
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario con is_active = 1 por defecto
    $sql = "INSERT INTO usuarios (nombre, email, password, tipo_usuario, is_active) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $password_hashed, $tipo_usuario);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "login.php?registro=exitoso");
    } else {
        // El código de error 1062 es para entradas duplicadas (email en este caso)
        if ($conn->errno == 1062) {
            header("Location: " . BASE_URL . "registro.php?error=emailtaken");
        } else {
            header("Location: " . BASE_URL . "registro.php?error=dberror");
        }
    }
    $stmt->close();
    $conn->close();
}
?>