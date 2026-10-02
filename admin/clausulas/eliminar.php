<?php
/*
--------------------------------------
 * CLÁUSULAS CONTRACTUALES - ELIMINAR
 * -----------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('clausulas/index.php');
}
csrf_verificar();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $conexion->prepare("DELETE FROM clausulas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows) {
    flash('La cláusula se eliminó correctamente.', 'eliminado');
} else {
    flash('La cláusula ya no existía.', 'error');
}
redirigir('clausulas/index.php');
