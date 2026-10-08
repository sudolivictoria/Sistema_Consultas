<?php
/*
   -------------------------------------------------
 * PARQUES - ELIMINAR
 * -------------------------------------------------
 * Un parque con arrendatarios NO se puede eliminar (la base lo impide por la
 * llave foránea): primero hay que cambiarlos de parque o eliminarlos.
 */
require_once __DIR__ . '/../inc/auth.php';

$stmt = $conexion->prepare("SELECT COUNT(*) FROM arrendatarios WHERE parque_id = ?");
$stmt->execute([(int) ($_POST['id'] ?? 0)]);
$total = $stmt->get_result()->fetch_row()[0];

if ($total > 0) {
    flash("No se puede eliminar: el parque tiene $total arrendatarios. Cámbielos de parque o elimínelos primero.", 'error');
    redirigir('parques/index.php');
}

eliminar_y_volver($conexion, 'parques', 'parques/index.php', 'El parque se eliminó correctamente.', 'El parque ya no existía.');
