<?php
/*
   -------------------------------------------------
 * CLÁUSULAS - ELIMINAR
 * -------------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

eliminar_y_volver($conexion, 'clausulas', 'clausulas/index.php', 'La cláusula se eliminó correctamente.', 'La cláusula ya no existía.');
