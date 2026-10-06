<?php
/*
  ----------------------------------------------
 * ARRENDATARIOS - ELIMINAR
 * -----------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';
//--METODO: eliminar_y_volver() está en admin/inc/bootstrap.php. Recibe la conexión, el nombre de la tabla, la URL a la que volver, y dos mensajes: uno si se eliminó y otro si ya no existía.
eliminar_y_volver($conexion, 'arrendatarios', 'arrendatarios/index.php', 'El arrendatario se eliminó correctamente.', 'El arrendatario ya no existía.');
