<?php
/*
   ------------------------------------------------------------------------
 * CONVENIOS - ELIMINAR
 * Todo el trabajo lo hace eliminar_y_volver() (ver admin/inc/bootstrap.php).
 * -----------------------------------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

eliminar_y_volver($conexion, 'convenios', 'convenios/index.php', 'El convenio se eliminó correctamente.', 'El convenio ya no existía.');
