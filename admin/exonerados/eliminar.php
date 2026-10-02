<?php
/*
   -----------------------------
 * EXONERADOS - ELIMINAR
 * -----------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('exonerados/index.php');
}
csrf_verificar();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $conexion->prepare("DELETE FROM exonerados_apulo WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows) {
    flash('El registro se eliminó correctamente.', 'eliminado');
} else {
    flash('El registro ya no existía.', 'error');
}
redirigir('exonerados/index.php');
