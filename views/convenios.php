<?php
/*
 *----------------------------------------------------------------------------
 *                      CONSULTA PÚBLICA DE CONVENIOS
 * ---------------------------------------------------------------------------
 * Tabla con buscador y filtros + ventana de detalle de cada convenio.
 * El buscador, los filtros y la ventana los maneja js/convenios.js.
 */
$titulo = 'Consulta de convenios';
require __DIR__ . '/../inc/publico_inicio.php';

$convenios = $conexion->query(
    "SELECT referencia, institucion, vigencia, descripcion, suscripcion, plazo, vencimiento,
            comentario, exoneracion, exoneracion_desc, promocion, promocion_desc
     FROM convenios ORDER BY referencia"
)->fetch_all(MYSQLI_ASSOC);   //----fetch_all = todas las filas de una vez, en un arreglo
?>

<div class="pagina-titulos">
    <nav class="ruta" aria-label="Ruta">Consultas / <span>Convenios</span></nav>
    <h1 class="titulo">Consulta de <span>convenios</span></h1>
    <p class="subtitulo">Busque por institución o referencia. Use «Ver» para abrir el detalle completo del convenio.</p>
</div>

<section class="panel">
    <div class="panel-busqueda">
        <label for="buscadorCustom">Buscar convenio</label>
        <!-----buscador con filtro------->
        <div class="busqueda-fila">
            <div class="campo-busqueda">
                <?= icono('buscar', 20, 2.2) ?>
                <input id="buscadorCustom" type="search" placeholder="Nombre de la institución o referencia…" autocomplete="off">
            </div>
            <!--data-tipo indica qué filtra cada botón (ver js/convenios.js)-->
            <div class="tabs tabs-alto" role="group" aria-label="Filtrar convenios">
                <button type="button" class="tab activa" data-tipo="todos" aria-pressed="true">Todos</button>
                <button type="button" class="tab" data-tipo="vigencia" data-val="SI" aria-pressed="false">Vigentes</button>
                <button type="button" class="tab" data-tipo="vigencia" data-val="NO" aria-pressed="false">No vigentes</button>
                <button type="button" class="tab" data-tipo="exprom" data-col="6" aria-pressed="false">Exoneración</button>
                <button type="button" class="tab" data-tipo="exprom" data-col="7" aria-pressed="false">Promoción</button>
            </div>
        </div>
    </div>

    <table id="tablaConvenios" class="tabla">
        <thead>
            <tr>
                <th scope="col">Ref.</th>
                <th scope="col" class="col-institucion">Institución</th>
                <th scope="col">Estado</th>
                <th scope="col">Suscripción</th>
                <th scope="col">Vencimiento</th>
                <th scope="col">Plazo</th>
                <th scope="col">Exoneración</th><!--columna oculta: solo para el filtro-->
                <th scope="col">Promoción</th><!--columna oculta: solo para el filtro-->
                <th scope="col" class="derecha">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($convenios as $c): ?>
                <?php
                $vigencia    = si_no($c['vigencia']);
                $exoneracion = si_no($c['exoneracion']);
                $promocion   = si_no($c['promocion']);
                $vencimiento = fecha_mostrar($c['vencimiento']);

                //----todos los datos que necesita la ventana de detalle, en un solo paquete.
                //----json_encode los convierte a texto JSON; jQuery lo lee con .data('convenio').
                //----Exoneración y promoción solo se envían si la columna dice SI.
                $detalle = [
                    'ref'   => $c['referencia'],
                    'inst'  => $c['institucion'],
                    'vig'   => $vigencia,
                    'sus'   => fecha_mostrar($c['suscripcion']),
                    'ven'   => $vencimiento,
                    'plazo' => $c['plazo'] ?: 'Indefinido',
                    'desc'  => $c['descripcion'],
                    'ex'    => $exoneracion === 'SI' ? $c['exoneracion_desc'] : '',
                    'prom'  => $promocion === 'SI' ? $c['promocion_desc'] : '',
                    'com'   => $c['comentario'],
                ];
                ?>
                <tr>
                    <td data-label="Ref."><span class="ref"><?= e($c['referencia']) ?></span></td>
                    <td data-label="Institución" class="fuerte"><?= e($c['institucion']) ?></td>
                    <!--data-search="SI/NO" es lo que usa el filtro Vigentes / No vigentes-->
                    <td data-label="Estado" data-search="<?= $vigencia ?>"><?= badge_vigencia($vigencia) ?></td>
                    <!--data-order = la fecha real, para que se ordene bien-->
                    <td data-label="Suscripción" class="numeros" data-order="<?= e($c['suscripcion']) ?>"><?= fecha_mostrar($c['suscripcion']) ?></td>
                    <td data-label="Vencimiento" class="numeros<?= $vencimiento === 'Indefinido' ? ' tenue' : '' ?>" data-order="<?= e($c['vencimiento']) ?>"><?= $vencimiento ?></td>
                    <td data-label="Plazo" class="tenue sin-cortar"><?= e($detalle['plazo']) ?></td>
                    <td><?= $exoneracion ?></td>
                    <td><?= $promocion ?></td>
                    <td data-label="Detalle" class="derecha">
                        <button type="button" class="btn-ver btn-detalle" aria-label="Ver detalle de <?= e($c['referencia']) ?>"
                            data-convenio="<?= e(json_encode($detalle)) ?>">
                            <?= icono('ojo', 16) ?> Ver
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!------VENTANA DETALLE CONVENIO-------->
<dialog id="modalDetalle" class="modal" aria-labelledby="m-inst">
    <div class="detalle-cabecera">
        <div class="detalle-fila">
            <div class="detalle-etiquetas">
                <span class="detalle-icono"><?= icono('documento', 20) ?></span>
                <span id="m-ref" class="ref-clara"></span>
                <span id="m-estado" class="estado"></span>
            </div>
            <button type="button" class="btn-x btn-cerrar-modal" aria-label="Cerrar"><?= icono('cerrar', 18, 2.2) ?></button>
        </div>
        <div>
            <span class="detalle-sobretitulo">Convenio institucional</span>
            <h2 id="m-inst" class="detalle-titulo"></h2>
        </div>
    </div>

    <div class="detalle-datos">
        <div class="detalle-dato"><small>Suscripción</small><strong id="m-sus"></strong></div>
        <div class="detalle-dato"><small>Vencimiento</small><strong id="m-ven"></strong></div>
        <div class="detalle-dato"><small>Plazo</small><strong id="m-plazo"></strong></div>
    </div>

    <!--------Secciones: se ocultan si vienen vacías. El color de cada círculo lo pone js/convenios.js, alternando azul y verde entre las que se ven----->
    <div class="detalle-cuerpo">
        <div id="padre-desc" class="detalle-seccion">
            <span class="detalle-circulo"><?= icono('documento') ?></span>
            <div class="detalle-texto"><strong>Descripción del convenio</strong><div id="m-desc"></div></div>
        </div>
        <div id="padre-ex" class="detalle-seccion">
            <span class="detalle-circulo"><?= icono('ticket') ?></span>
            <div class="detalle-texto"><strong>Exoneración</strong><div id="m-ex"></div></div>
        </div>
        <div id="padre-prom" class="detalle-seccion">
            <span class="detalle-circulo"><?= icono('etiqueta') ?></span>
            <div class="detalle-texto"><strong>Promoción</strong><div id="m-prom"></div></div>
        </div>
        <div id="padre-com" class="detalle-seccion">
            <span class="detalle-circulo"><?= icono('mensaje') ?></span>
            <div class="detalle-texto"><strong>Comentarios y aclaraciones</strong><div id="m-com"></div></div>
        </div>
    </div>

    <div class="modal-pie">
        <button type="button" class="btn btn-oscuro btn-cerrar-modal">Cerrar</button>
    </div>
</dialog>

<?php
$script = 'convenios.js';
require __DIR__ . '/../inc/publico_fin.php';
