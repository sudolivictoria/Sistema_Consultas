<?php
/*
 * ============================================================================
 * ---------------------------AUTH---------------------------------------------
 * ============================================================================
 * Toda página del admin (excepto el login) empieza con: require_once __DIR__ . '/../inc/auth.php';
 * Si la persona no ha iniciado sesión, la mandamos al login 
 */

require_once __DIR__ . '/bootstrap.php';

//---$_SESSION['usuario_id'] solo existe si el login fue correcto (ver admin/index.php)
if (empty($_SESSION['usuario_id'])) {
    redirigir('index.php');   //-------redirigir() incluye exit: aquí se detiene todo
}
