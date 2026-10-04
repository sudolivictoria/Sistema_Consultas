<?php
/*
   ------------------------------------------------------------------
 *                     EXONERADOS - ELIMINAR
 * ------------------------------------------------------------------
 * Todo el trabajo lo hace eliminar_y_volver() (ver admin/inc/bootstrap.php).
 */
require_once __DIR__ . '/../inc/auth.php';

eliminar_y_volver($conexion, 'exonerados_apulo', 'exonerados/index.php', 'El registro se eliminó correctamente.', 'El registro ya no existía.');
