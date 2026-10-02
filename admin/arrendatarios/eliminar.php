<?php
/*
----------------------------------
 * ARRENDATARIOS - ELIMINAR
 * -------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('arrendatarios/index.php');
}
csrf_verificar();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $conexion->prepare("DELETE FROM arrendatarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows) {
    flash('El arrendatario se eliminó correctamente.', 'eliminado');
} else {
    flash('El arrendatario ya no existía.', 'error');
}
redirigir('arrendatarios/index.php');
