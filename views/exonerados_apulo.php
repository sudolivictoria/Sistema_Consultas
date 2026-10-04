<?php
/*
 *----------------------------------------------------------------------------
 *                      CONSULTA PÚBLICA DE EXONERADOS (Apulo)
 * ---------------------------------------------------------------------------
 * Tabla con buscador por nombre o DUI. El buscador lo maneja js/exonerados_apulo.js.
 */
$titulo = 'Consulta de exonerados';
require __DIR__ . '/../inc/publico_inicio.php';

//----obtener todos los exonerados de la base de datos
$exonerados = $conexion->query(
    "SELECT dui, nombre, comunidad FROM exonerados_apulo ORDER BY nombre COLLATE utf8mb4_unicode_ci"
)->fetch_all(MYSQLI_ASSOC);
?>

<div class="pagina-titulos">
    <nav class="ruta" aria-label="Ruta">Consultas / Apulo / <span>Exonerados</span></nav>
    <div class="titulo-linea">
        <h1 class="titulo">Consulta de <span>exonerados</span></h1>
        <span class="insignia">Apulo</span>
    </div>
    <p class="subtitulo">Verifique si una persona está exonerada escribiendo su nombre o su número de DUI completo.</p>
</div>

<section class="panel">
    <div class="panel-busqueda">
        <label for="buscadorCustom">¿La persona está exonerada?</label>
        <div class="busqueda-fila">
            <div class="campo-busqueda">
                <?= icono('buscar', 20, 2.2) ?>
                <input id="buscadorCustom" type="search" placeholder="Nombre o número de DUI…" autocomplete="off">
            </div>
        </div>
        <span class="nota-verde">El DUI puede escribirse con o sin guion.</span>
    </div>

    <!--------------------------------TABLE---------------------------------->
    <table id="tablaExonerados" class="tabla">
        <thead>
            <tr>
                <th scope="col" class="col-dui">DUI</th>
                <th scope="col">Nombre completo</th>
                <th scope="col">Comunidad</th>
            </tr>
        </thead>
        <tbody>
            <!--exonerados es un arreglo-->
            <?php foreach ($exonerados as $x): ?>
                <tr>
                    <!--DATASEARCH el dui se encuentra con o sin guion-->
                    <td data-label="DUI" class="mono" data-search="<?= e($x['dui'] . ' ' . str_replace('-', '', $x['dui'] ?? '')) ?>"><?= e($x['dui']) ?></td>
                    <td data-label="Nombre" class="fuerte"><?= e($x['nombre']) ?></td>
                    <td data-label="Comunidad">
                        <?php if ($x['comunidad']): ?>
                            <span class="pastilla"><?= e($x['comunidad']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php
$script = 'exonerados_apulo.js';
require __DIR__ . '/../inc/publico_fin.php';
