<?php
/*
 * ============================================================================
 *                         CONVENIOS - DELETE
 * ============================================================================
 * No muestra nada: recibe el id por POST, borra y regresa al listado.
 */
require_once __DIR__ . '/../inc/auth.php';

//--si abren directamente el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('convenios/index.php');
}
csrf_verificar();   //----confirma que el formulario salió de nuestro listado

$id = (int) ($_POST['id'] ?? 0);

$stmt = $conexion->prepare("DELETE FROM convenios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

//----Cuantas filas se eliminaron 
if ($stmt->affected_rows) {
    flash('El convenio se eliminó correctamente.', 'eliminado');
} else {
    flash('El convenio ya no existía.', 'error');
}
redirigir('convenios/index.php');
