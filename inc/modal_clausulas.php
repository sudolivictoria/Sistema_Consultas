<?php
/*
 *----------------------------------------------------------------------------
 *              VENTANA DE CLÁUSULAS CONTRACTUALES (consulta pública)
 * ---------------------------------------------------------------------------
 * La incluye views/arrendatarios.php. Trae sus propias cláusulas de la base.
 * Necesita de la página: $conexion y $hayPdf (si existe el PDF de normativa interna).
 * La abre el botón #btnClausulas (ver js/arrendatarios.js).
 */

/** @var mysqli $conexion */
/** @var bool $hayPdf */

$clausulas = $conexion->query("SELECT numero, titulo, texto FROM clausulas ORDER BY numero")->fetch_all(MYSQLI_ASSOC);

//---nombres ordinales para las clausulas
$ordinales = [1 => 'primera', 'segunda', 'tercera', 'cuarta', 'quinta', 'sexta', 'séptima', 'octava', 'novena', 'décima'];
?>
<dialog id="modalClausulas" class="modal modal-ancho" aria-labelledby="titulo-clausulas">
    <div class="modal-cabecera">
        <div class="icono-caja"><?= icono('documento', 22) ?></div>
        <div class="modal-titulos">
            <h2 id="titulo-clausulas">Cláusulas contractuales</h2>
            <span>Contratos de arrendamiento ISTU · <?= count($clausulas) ?> <?= count($clausulas) === 1 ? 'cláusula' : 'cláusulas' ?></span>
        </div>
        <button type="button" class="btn-cuadrado btn-cerrar-modal" aria-label="Cerrar"><?= icono('cerrar', 18, 2.2) ?></button>
    </div>

    <div class="modal-cuerpo">
        <?php if (!$clausulas): ?>
            <div class="tabla-vacia">
                <strong>Sin cláusulas</strong>
                <span>Aún no se han registrado cláusulas contractuales.</span>
            </div>
        <?php endif; ?>

        <?php foreach ($clausulas as $c): ?>
            <?php $numero = (int) $c['numero']; ?>
            <!--Todas empiezan cerradas: clic en el título para abrir-->
            <details class="acordeon">
                <summary>
                    <span class="acordeon-numero"><?= $numero ?></span>
                    <span class="acordeon-titulos">
                        <small>Cláusula <?= $ordinales[$numero] ?? $numero ?></small>
                        <strong><?= e($c['titulo']) ?></strong>
                    </span>
                    <span class="acordeon-flecha"><?= icono('chevron-abajo', 18, 2.2) ?></span>
                </summary>
                <!--formatear_incisos() pone cada "a)", "b)"... en su propia línea (ver inc/funciones.php)-->
                <div class="acordeon-texto"><?= formatear_incisos($c['texto']) ?></div>
            </details>
        <?php endforeach; ?>
    </div>

    <!--Pie de la ventana: si hay PDF, se muestra el botón; si no, solo el botón de cerrar-->
    <div class="modal-pie">
        <?php if ($hayPdf): ?>
            <a href="../<?= version('uploads/normativa-interna.pdf') ?>" target="_blank" rel="noopener" class="btn btn-contorno">
                <?= icono('pdf', 17) ?> Normativa interna (PDF) <?= icono('externo', 14, 2.2) ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-oscuro btn-cerrar-modal">Entendido</button>
    </div>
</dialog>
