<?php
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    
    if (empty($query)) {
        exit();
    }
    
    $search_term = "%" . $query . "%";
    // Añadimos la condición is_active = 1 para no mostrar ofertas inactivas
    $sql = "SELECT o.id, o.titulo, u.nombre AS nombre_empresa 
            FROM ofertas o
            JOIN usuarios u ON o.id_empresa = u.id
            WHERE (o.titulo LIKE ? OR u.nombre LIKE ?) AND o.is_active = 1
            LIMIT 5";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // El enlace ahora apunta directamente a la oferta usando BASE_URL
            echo '<a href="' . BASE_URL . 'oferta.php?id=' . $row['id'] . '" class="search-result-item">';
            echo '  <div class="title">' . htmlspecialchars($row['titulo']) . '</div>';
            echo '  <div class="company">' . htmlspecialchars($row['nombre_empresa']) . '</div>';
            echo '</a>';
        }
    } else {
        echo '<div class="search-result-item"><span class="company">No se encontraron resultados.</span></div>';
    }
    
    $stmt->close();
    $conn->close();
}
?>