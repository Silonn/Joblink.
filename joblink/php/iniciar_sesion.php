<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, nombre, email, password, tipo_usuario FROM usuarios WHERE email = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
            header("Location: " . BASE_URL . "dashboard.php");
            exit();
        }
    }
    
    // Si el bucle termina sin éxito, las credenciales son incorrectas.
    header("Location: " . BASE_URL . "login.php?error=credenciales");
    exit();
}
?>