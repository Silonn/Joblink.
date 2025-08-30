<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . 'includes/conexion.php';

// Seguridad: Solo para candidatos logueados
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo_usuario'] != 'candidato') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id'];

    $conn->begin_transaction();

    try {
        // 1. Actualizar Datos Personales (ya era seguro)
        $stmt_user = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, sitio_web = ?, resumen_perfil = ? WHERE id = ?");
        $stmt_user->bind_param("sssssi", $_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['sitio_web'], $_POST['resumen_perfil'], $id_usuario);
        $stmt_user->execute();
        $stmt_user->close();

        // --- GESTIÓN SEGURA Y EFICIENTE DE SUB-TABLAS ---

        // 2. Gestionar Experiencia Laboral
        $exp_ids_form = isset($_POST['exp_id']) ? array_filter($_POST['exp_id'], fn($id) => $id != 'new') : [];
        $stmt_get_exp = $conn->prepare("SELECT id FROM cv_experiencia WHERE id_usuario = ? AND is_active = 1");
        $stmt_get_exp->bind_param("i", $id_usuario);
        $stmt_get_exp->execute();
        $exp_ids_db_result = $stmt_get_exp->get_result();
        $exp_ids_db = [];
        while($row = $exp_ids_db_result->fetch_assoc()) { $exp_ids_db[] = $row['id']; }
        $exp_ids_to_deactivate = array_diff($exp_ids_db, $exp_ids_form);
        
        if (!empty($exp_ids_to_deactivate)) {
            $stmt_deactivate_exp = $conn->prepare("UPDATE cv_experiencia SET is_active = 0 WHERE id = ? AND id_usuario = ?");
            foreach ($exp_ids_to_deactivate as $id_to_deactivate) {
                $stmt_deactivate_exp->bind_param("ii", $id_to_deactivate, $id_usuario);
                $stmt_deactivate_exp->execute();
            }
        }

        $stmt_update_exp = $conn->prepare("UPDATE cv_experiencia SET puesto=?, empresa=?, fecha_inicio=?, fecha_fin=?, descripcion=?, is_active=1 WHERE id=? AND id_usuario=?");
        $stmt_insert_exp = $conn->prepare("INSERT INTO cv_experiencia (id_usuario, puesto, empresa, fecha_inicio, fecha_fin, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
        if (isset($_POST['exp_puesto'])) {
            for ($i = 0; $i < count($_POST['exp_puesto']); $i++) {
                $id = $_POST['exp_id'][$i];
                $puesto = $_POST['exp_puesto'][$i]; $empresa = $_POST['exp_empresa'][$i];
                $inicio = !empty($_POST['exp_inicio'][$i]) ? $_POST['exp_inicio'][$i] : null;
                $fin = !empty($_POST['exp_fin'][$i]) ? $_POST['exp_fin'][$i] : null;
                $desc = $_POST['exp_desc'][$i];
                if ($id == 'new') {
                    $stmt_insert_exp->bind_param("isssss", $id_usuario, $puesto, $empresa, $inicio, $fin, $desc);
                    $stmt_insert_exp->execute();
                } else {
                    $stmt_update_exp->bind_param("sssssii", $puesto, $empresa, $inicio, $fin, $desc, $id, $id_usuario);
                    $stmt_update_exp->execute();
                }
            }
        }

        // 3. Gestionar Educación
        $edu_ids_form = isset($_POST['edu_id']) ? array_filter($_POST['edu_id'], fn($id) => $id != 'new') : [];
        $stmt_get_edu = $conn->prepare("SELECT id FROM cv_educacion WHERE id_usuario = ? AND is_active = 1");
        $stmt_get_edu->bind_param("i", $id_usuario);
        $stmt_get_edu->execute();
        $edu_ids_db_result = $stmt_get_edu->get_result();
        $edu_ids_db = [];
        while($row = $edu_ids_db_result->fetch_assoc()) { $edu_ids_db[] = $row['id']; }
        $edu_ids_to_deactivate = array_diff($edu_ids_db, $edu_ids_form);

        if (!empty($edu_ids_to_deactivate)) {
            $stmt_deactivate_edu = $conn->prepare("UPDATE cv_educacion SET is_active = 0 WHERE id = ? AND id_usuario = ?");
            foreach ($edu_ids_to_deactivate as $id_to_deactivate) {
                $stmt_deactivate_edu->bind_param("ii", $id_to_deactivate, $id_usuario);
                $stmt_deactivate_edu->execute();
            }
        }

        $stmt_update_edu = $conn->prepare("UPDATE cv_educacion SET institucion=?, titulo=?, fecha_inicio=?, fecha_fin=?, is_active=1 WHERE id=? AND id_usuario=?");
        $stmt_insert_edu = $conn->prepare("INSERT INTO cv_educacion (id_usuario, institucion, titulo, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)");
        if (isset($_POST['edu_institucion'])) {
            for ($i = 0; $i < count($_POST['edu_institucion']); $i++) {
                $id = $_POST['edu_id'][$i];
                $ins = $_POST['edu_institucion'][$i]; $tit = $_POST['edu_titulo'][$i];
                $inicio = !empty($_POST['edu_inicio'][$i]) ? $_POST['edu_inicio'][$i] : null;
                $fin = !empty($_POST['edu_fin'][$i]) ? $_POST['edu_fin'][$i] : null;
                if ($id == 'new') {
                    $stmt_insert_edu->bind_param("issss", $id_usuario, $ins, $tit, $inicio, $fin);
                    $stmt_insert_edu->execute();
                } else {
                    $stmt_update_edu->bind_param("ssssii", $ins, $tit, $inicio, $fin, $id, $id_usuario);
                    $stmt_update_edu->execute();
                }
            }
        }

        // 4. Gestionar Habilidades (delete/insert es aceptable aquí, pero lo haremos con soft-delete)
        $stmt_deactivate_all_hab = $conn->prepare("UPDATE cv_habilidades SET is_active = 0 WHERE id_usuario = ?");
        $stmt_deactivate_all_hab->bind_param("i", $id_usuario);
        $stmt_deactivate_all_hab->execute();

        if (!empty($_POST['habilidades'])) {
            $habilidades = explode(",", $_POST['habilidades']);
            $stmt_upsert_hab = $conn->prepare("INSERT INTO cv_habilidades (id_usuario, habilidad, is_active) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
            foreach ($habilidades as $habilidad) {
                $hab_trimmed = trim($habilidad);
                if (!empty($hab_trimmed)) {
                    $stmt_upsert_hab->bind_param("is", $id_usuario, $hab_trimmed);
                    $stmt_upsert_hab->execute();
                }
            }
        }

        $conn->commit();
        header('Location: ' . BASE_URL . 'crear-cv.php?status=guardado');

    } catch (Exception $e) {
        $conn->rollback();
        // Para depuración: error_log($e->getMessage());
        header('Location: ' . BASE_URL . 'crear-cv.php?error=db_transaction');
    }

    $conn->close();

} else {
    header('Location: ' . BASE_URL . 'dashboard.php');
}
?>