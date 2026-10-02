<?php
/*
 * ============================================================================
 * PARTE DE ARRIBA DE LAS CONSULTAS PÚBLICAS (views/*.php)
 * ============================================================================
 * Conecta a la base, carga las funciones y dibuja el encabezado.
 */
require_once __DIR__ . '/../conexion.php';   
require_once __DIR__ . '/funciones.php';      
require_once __DIR__ . '/iconos.php';         

$titulo ??= 'Consultas';  
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= e($titulo) ?> - ISTU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <?= estilos('../', 'base', 'paginas', 'tablas', 'modales', 'preloader') ?>
    <link rel="icon" type="image/png" href="../images/logo.png" />
    <script src="../<?= version('js/preloader.js') ?>"></script>
</head>

<body class="fondo-cuadricula">
    <?php require __DIR__ . '/preloader.php'; ?>

    <div class="barra-acento"></div>

    <header class="encabezado">
        <div class="encabezado-contenido">
            <div class="marca">
                <div class="marca-logo">ISTU</div>
                <div class="marca-texto">
                    <strong>Instituto Salvadoreño de Turismo</strong>
                    <span>Portal de consultas internas</span>
                </div>
            </div>
            <a href="../index.php" class="btn btn-inicio">
                <?= icono('flecha-izq', 16) ?>
                Inicio
            </a>
        </div>
    </header>

    <main class="contenedor">
