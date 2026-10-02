<?php
/*
 * ============================================================================
 * PANEL LATERAL (drawer) - parte de arriba
 * ============================================================================
 * El formulario de crear/editar se muestra como un panel que se desliza desde la derecha.
 * Para subir archivos, antes se define $subeArchivos = true (agrega enctype).
 * "Cerrar" es simplemente un enlace a index.php (el listado sin panel).
 */

$tituloForm   ??= 'Formulario';
$subeArchivos ??= false;
?>
<div class="drawer-fondo">
    <!-----La zona oscura de la izquierda también cierra (es un enlace al listado)----->
    <a href="index.php" class="drawer-cerrar-fondo" aria-label="Cerrar formulario" tabindex="-1"></a>

    <div class="drawer" role="dialog" aria-modal="true" aria-labelledby="titulo-form">
        <div class="drawer-acento"></div>

        <div class="drawer-cabecera">
            <div>
                <h2 id="titulo-form"><?= e($tituloForm) ?></h2>
                <span>Los campos marcados como opcionales pueden quedar vacíos.</span>
            </div>
            <!--id="cerrarDrawer": admin.js lo usa para cerrar también con la tecla Escape-->
            <a href="index.php" id="cerrarDrawer" class="btn-cuadrado" aria-label="Cerrar"><?= icono('cerrar', 18, 2.2) ?></a>
        </div>
        <!--Sin action="": se envía a la misma URL (form.php o form.php?id=5)-->
        <form method="post" class="drawer-form" <?= $subeArchivos ? 'enctype="multipart/form-data"' : '' ?>>
            <?= csrf_campo() ?>
            <div class="drawer-campos">
                <?php require __DIR__ . '/errores.php'; ?>
