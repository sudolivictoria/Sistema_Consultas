<?php
/*
 * ============================================================================
 *        PARTE DE ARRIBA DE LAS CONSULTAS PÚBLICAS (views/*.php)
 * ============================================================================
 * Conecta a la base, carga las funciones y dibuja el encabezado.
 */
require_once __DIR__ . '/../conexion.php';   
require_once __DIR__ . '/funciones.php';      
require_once __DIR__ . '/iconos.php';         

$titulo = ($titulo ?? 'Consultas') . ' - ISTU';
$raiz   = '../';
$hojas  = ['base', 'paginas', 'tablas', 'modales', 'preloader'];
require __DIR__ . '/head.php';
?>

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
