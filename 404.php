<?php
/*
 * ============================================================================
 *                      PÁGINA 404 - NO ENCONTRADA
 * ============================================================================
 */
require __DIR__ . '/inc/iconos.php';
require __DIR__ . '/inc/funciones.php';

//----el código 404 le avisa al navegador y a los buscadores que la página no existe.
//----Sin esto, el servidor respondería 200 ("todo bien") aunque se muestre un error.
http_response_code(404);

$titulo = 'Página no encontrada - ISTU';
$raiz   = SITE_URL . '/';
$hojas  = ['base', 'inicio', 'preloader'];
require __DIR__ . '/inc/head.php';
?>

<body class="fondo-cuadricula">
    <?php require __DIR__ . '/inc/preloader.php'; ?>

    <div class="barra-acento-gruesa"></div>

    <main class="inicio-main">
        <div class="hero">
            <span class="error-codigo">404</span>
            <h1>Página no <span>encontrada</span></h1>
            <span class="hero-linea"></span>
            <p>La dirección que buscó no existe o fue movida.</p>
            <a href="<?= SITE_URL ?>/index.php" class="btn btn-primario">
                <?= icono('flecha-izq', 16, 2.2) ?>
                Volver al inicio
            </a>
        </div>

        <p class="pie-texto">Unidad Jurídica · ISTU</p>
    </main>
</body>

</html>
