<?php
/*
 *-------------------------------------------
 * ARRENDATARIOS - LISTADO 
 * Lo usan index.php, form.php y politica.php.
 * ------------------------------------------
 */

//---si alguien abre listado.php directamente (sin pasar por index.php y auth.php), lo mandamos al index
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
//---buscamos todos los arrendatarios en la base de datos
$resultado = $conexion->query(
    "SELECT id, nombre_arrendatario, local_comercial, venta_autorizada FROM arrendatarios
     ORDER BY nombre_arrendatario"
);

//----contamos cuántas cláusulas hay en la base de datos y si existe el PDF de política interna
$totalClausulas = $conexion->query("SELECT COUNT(*) FROM clausulas")->fetch_row()[0];
$hayPdf = file_exists(POLITICA_PDF_RUTA);   //---POLITICA_PDF_RUTA está en inc/funciones.php. Es la ruta absoluta al PDF de política interna.
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <span>Arrendatarios</span></nav>
        <h1 class="titulo titulo-admin">Administrar <span>arrendatarios</span></h1>
        <p class="subtitulo">Arrendatarios <?= date('Y') ?> y su venta autorizada por local comercial.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nuevo arrendatario</a>
</div>

<!--NORMATIVA: lo que se muestra a todos en la consulta pública-->
<section class="normativa">
    <div class="normativa-encabezado">
        <h2>Normativa</h2>
        <span>Se muestra a todos los arrendatarios en la consulta pública.</span>
    </div>
    <div class="normativa-grid">
        <div class="normativa-item">
            <span class="normativa-icono"><?= icono('lista', 20) ?></span>
            <div class="normativa-texto">
                <strong>Cláusulas contractuales</strong>
                <span><?= $totalClausulas ?> cláusulas</span>
            </div>
            <a href="../clausulas/index.php" class="btn btn-chico btn-suave">Editar</a>
        </div>
        <div class="normativa-item">
            <span class="normativa-icono"><?= icono('pdf', 20) ?></span>
            <div class="normativa-texto">
                <strong>Política interna</strong>
                <?php if ($hayPdf): ?>
                    <span><a href="<?= POLITICA_PDF_URL ?>" target="_blank" rel="noopener">politica-interna.pdf</a></span>
                <?php else: ?>
                    <span>Aún no se ha subido</span>
                <?php endif; ?>
            </div>
            <a href="politica.php" class="btn btn-chico btn-suave"><?= $hayPdf ? 'Reemplazar PDF' : 'Subir PDF' ?></a>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel-herramientas">
        <div class="campo-busqueda campo-busqueda-chico">
            <label for="buscarTabla" class="solo-lectores">Buscar arrendatario o local</label>
            <?= icono('buscar', 18, 2.2) ?>
            <input id="buscarTabla" type="search" placeholder="Buscar arrendatario o local…" autocomplete="off">
        </div>
        <span class="contador"><?= $resultado->num_rows ?> registros</span>
    </div>

    <table class="tabla tabla-admin">
        <thead>
            <tr>
                <th scope="col">Arrendatario</th>
                <th scope="col">Local comercial</th>
                <th scope="col">Venta autorizada</th>
                <th scope="col" class="derecha">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($f = $resultado->fetch_assoc()): ?>
                <?php
                $productos = lista_desde_comas($f['venta_autorizada']);
                $nombre = $f['nombre_arrendatario'];   //---se usa en "¿Eliminar a ...?" y en las etiquetas de los botones
                ?>
                <tr>
                    <!--texto_o_indefinido(): si el dato viene vacío o NULL, muestra "Indefinido"-->
                    <td class="fuerte" data-label="Arrendatario"><?= e($f['nombre_arrendatario']) ?></td>
                    <td data-label="Local"><?= texto_o_indefinido($f['local_comercial']) ?></td>
                    <td data-label="Venta">
                        <?php if ($productos): ?>
                            <!--la venta se parte por las comas y cada producto se muestra como etiqueta-->
                            <div class="chips">
                                <?php foreach ($productos as $producto): ?>
                                    <span class="chip"><?= e($producto) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?= texto_o_indefinido(null) ?>
                        <?php endif; ?>
                    </td>
                    <td class="acciones" data-label="Acciones">
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar a <?= e($nombre) ?>"><?= icono('editar', 16) ?></a>
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar este arrendatario?" data-nombre="<?= e($nombre) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar a <?= e($nombre) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
