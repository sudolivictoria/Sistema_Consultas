<?php
/*
 * ------------------CERRAR SESIÓN------------------
 */
require_once __DIR__ . '/inc/bootstrap.php';

$_SESSION = [];      //------borra todos los datos (usuario_id, nombre, token...)
session_destroy();   //------elimina la sesión del servidor

redirigir('index.php');
