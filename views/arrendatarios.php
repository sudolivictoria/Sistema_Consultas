<?php
/*
 * CONSULTA PÚBLICA DE ARRENDATARIOS
 * Tabla con buscador + normativa (PDF de política interna y ventana de cláusulas).
 * El buscador y la ventana los maneja js/arrendatarios.js.
 */
$titulo = 'Consulta de arrendatarios';
require __DIR__ . '/../inc/publico_inicio.php';

$arrendatarios = $conexion->query(
    "SELECT nombre_arrendatario, local_comercial, venta_autorizada FROM arrendatarios ORDER BY nombre_arrendatario"
)->fetch_all(MYSQLI_ASSOC);

$clausulas = $conexion->query("SELECT numero, titulo, texto FROM clausulas ORDER BY numero")->fetch_all(MYSQLI_ASSOC);

//---revisa si el archivo de politica interna existe
$hayPdf = file_exists(POLITICA_PDF_RUTA);

//---nombres ordinales para las clausulas
$ordinales = [1 => 'primera', 'segunda', 'tercera', 'cuarta', 'quinta', 'sexta', 'séptima', 'octava', 'novena', 'décima'];
?>

<div class="pagina-titulos">
    <nav class="ruta" aria-label="Ruta">Consultas / <span>Arrendatarios</span></nav>
    <div class="titulo-linea">
        <h1 class="titulo">Consulta de <span>arrendatarios</span></h1>
        <span class="insignia"><?= date('Y') ?></span>
    </div>
    <p class="subtitulo">Verifique qué servicios tiene autorizados cada local comercial durante la supervisión de contratos.</p>
</div>

<section class="panel">
    <div class="panel-busqueda">
        <label for="buscadorCustom">Buscar arrendatario, local o servicio</label>
        <div class="busqueda-fila">
            <div class="campo-busqueda">
                <?= icono('buscar', 20, 2.2) ?>
                <input id="buscadorCustom" type="search" placeholder="Ej.: nombre del local o un producto…" autocomplete="off">
            </div>
        </div>

        <!--------------------POLITICA INTERNA------------>
        <div class="normativa-fila">
            <span>Normativa:</span>
            <?php if ($hayPdf): ?>
                <a href="../<?= version('uploads/politica-interna.pdf') ?>" target="_blank" rel="noopener" class="btn btn-contorno btn-normativa">
                    <?= icono('pdf', 17) ?> Política interna <span class="mini-insignia">PDF</span> <?= icono('externo', 14, 2.2) ?>
                </a>
            <?php else: ?>
                <!--sin pdf el boton de la normativa esta desactivado-->
                <button type="button" class="btn btn-contorno btn-normativa" disabled title="Aún no se ha publicado">
                    <?= icono('pdf', 17) ?> Política interna <span class="mini-insignia">PDF</span>
                </button>
            <?php endif; ?>
            <button type="button" id="btnClausulas" class="btn btn-primario btn-normativa">
                <?= icono('lista', 17) ?> Cláusulas contractuales
            </button>
        </div>
    </div>

    <!------TABLA DE ARRENDATARIOS: con buscador y filtros manejados por js/arrendatarios.js------>
    <table id="tablaArrendatarios" class="tabla">
        <thead>
            <tr>
                <th scope="col" style="width: 28%">Arrendatario</th>
                <th scope="col" style="width: 26%">Local comercial</th>
                <th scope="col">Venta autorizada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arrendatarios as $a): ?>
                <tr>
                    <td data-label="Arrendatario" class="fuerte arriba"><?= e($a['nombre_arrendatario']) ?></td>
                    <td data-label="Local" class="arriba">
                        <span class="con-icono"><?= icono('local', 16) ?><?= e($a['local_comercial']) ?></span>
                    </td>
                    <td data-label="Venta" class="arriba">
                        <!--ETIQUETA X PRODUCTO-->
                        <div class="chips">
                            <?php foreach (lista_desde_comas($a['venta_autorizada']) as $producto): ?>
                                <span class="chip"><?= e($producto) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!---------------VENTANA DE CLÁUSULAS-------------->
<dialog id="modalClausulas" class="modal modal-ancho" aria-labelledby="titulo-clausulas">
    <div class="modal-cabecera">
        <div class="icono-caja"><?= icono('documento', 22) ?></div>
        <div class="modal-titulos">
            <h2 id="titulo-clausulas">Cláusulas contractuales</h2>
            <span>Contratos de arrendamiento ISTU · <?= count($clausulas) ?> cláusulas</span>
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
            <a href="../<?= version('uploads/politica-interna.pdf') ?>" target="_blank" rel="noopener" class="btn btn-contorno">
                <?= icono('pdf', 17) ?> Política interna (PDF) <?= icono('externo', 14, 2.2) ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-oscuro btn-cerrar-modal">Entendido</button>
    </div>
</dialog>

<?php
$script = 'arrendatarios.js';
require __DIR__ . '/../inc/publico_fin.php';
