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

//----conexion a la base de datos (ver la explicación del try/catch en conexion.php)
try {
    $conexion = new mysqli($host, $usuario, $password, $base_datos);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $ex) {
    error_log('Error de conexión a MySQL: ' . $ex->getMessage());
    http_response_code(500);
    exit('El sistema no está disponible en este momento. Intente más tarde.');
}
