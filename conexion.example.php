<?php
/*
 * PLANTILLA DE CONEXIÓN
 * Copiar este archivo como conexion.php y poner los datos reales.
 * conexion.php NO se sube al repositorio (está en .gitignore).
 */

//------configuración de la base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "exonerados";

//----conexion a la base de datos
$conexion = new mysqli($host, $usuario, $password, $base_datos);

//---verificar la conexion
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
