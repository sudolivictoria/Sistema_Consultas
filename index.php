<?php
require __DIR__ . '/inc/iconos.php';
require __DIR__ . '/inc/funciones.php';

$titulo = 'Consultas ISTU';
$raiz   = '';
$hojas  = ['base', 'inicio', 'preloader'];
require __DIR__ . '/inc/head.php';
?>

<body class="fondo-cuadricula">
    <?php require __DIR__ . '/inc/preloader.php'; ?>

    <!-------------------ENLACE PANEL DE ADMINISTRACIÓN------------------->
    <a href="admin/" class="enlace-admin"><?= icono('candado', 15) ?> Administración</a>
    <div class="barra-acento-gruesa"></div>

    <main class="inicio-main">
        <div class="hero">
            <span class="hero-insignia">Instituto Salvadoreño de Turismo</span>
            <h1>Plataforma de Consultas <span>ISTU</span></h1>
            <span class="hero-linea"></span>
            <p>Consulta y visualización de información institucional.</p>
        </div>

        <div class="tarjetas-inicio">
            <a href="views/exonerados_apulo.php" class="tarjeta-inicio">
                <span class="tarjeta-icono"><?= icono('persona-check', 30) ?></span>
                <span class="tarjeta-nombre">Exonerados Apulo</span>
                <span class="tarjeta-desc">Personas exoneradas en Apulo, por nombre o DUI.</span>
                <span class="tarjeta-cta">Consultar <?= icono('flecha-der', 16, 2.2) ?></span>
            </a>

            <a href="views/convenios.php" class="tarjeta-inicio">
                <span class="tarjeta-icono"><?= icono('documento', 30) ?></span>
                <span class="tarjeta-nombre">Convenios</span>
                <span class="tarjeta-desc">Convenios institucionales con su vigencia, plazo y beneficios.</span>
                <span class="tarjeta-cta">Consultar <?= icono('flecha-der', 16, 2.2) ?></span>
                <!--icon name, size, width-->
            </a>

            <a href="views/arrendatarios.php" class="tarjeta-inicio">
                <span class="tarjeta-nuevo">Nuevo</span>
                <span class="tarjeta-icono"><?= icono('tienda', 30) ?></span>
                <span class="tarjeta-nombre">Arrendatarios</span>
                <span class="tarjeta-desc">Venta autorizada por local comercial para la supervisión de contratos.</span>
                <span class="tarjeta-cta">Consultar <?= icono('flecha-der', 16, 2.2) ?></span>
            </a>
        </div>

        <p class="pie-texto">Unidad Jurídica · ISTU</p>
    </main>
</body>

</html>
