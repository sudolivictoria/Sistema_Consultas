<?php
/*
 * ARRENDATARIOS - LISTADO (pieza reutilizable)
 * Mismo patrón que convenios/listado.php, más la sección "Normativa" con
 * accesos a las cláusulas contractuales y al PDF de política interna.
 * Lo usan index.php, form.php y politica.php.
 */

// Esta pieza solo se incluye desde otras páginas. Si alguien la abre directo
// en el navegador (sin sesión ni diseño), lo mandamos al listado completo.
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
$resultado = $conexion->query("SELECT id, nombre_arrendatario, local_comercial, venta_autorizada FROM arrendatarios ORDER BY nombre_arrendatario");

// Datos para las tarjetas de normativa
$totalClausulas = $conexion->query("SELECT COUNT(*) FROM clausulas")->fetch_row()[0];
$hayPdf = file_exists(POLITICA_PDF_RUTA);   // file_exists: ¿el archivo está en el disco?
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <span>Arrendatarios</span></nav>
        <h1 class="titulo titulo-admin">Administrar <span>arrendatarios</span></h1>
        <p class="subtitulo">Arrendatarios <?= date('Y') ?> y su venta autorizada por local comercial.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nuevo arrendatario</a>
</div>

<!-- NORMATIVA: lo que se muestra a todos en la consulta pública -->
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
                <tr>
                    <td class="fuerte" data-label="Arrendatario"><?= e($f['nombre_arrendatario']) ?></td>
                    <td data-label="Local"><?= e($f['local_comercial']) ?></td>
                    <td data-label="Venta">
                        <div class="chips">
                            <?php
                            // La venta autorizada se guarda como "Agua, Gaseosas, Dulces".
                            // La partimos por las comas y mostramos cada producto como etiqueta.
                            foreach (lista_desde_comas($f['venta_autorizada']) as $producto):
                            ?>
                                <span class="chip"><?= e($producto) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="acciones" data-label="Acciones">
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar a <?= e($f['nombre_arrendatario']) ?>"><?= icono('editar', 16) ?></a>
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar este arrendatario?" data-nombre="<?= e($f['nombre_arrendatario']) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar a <?= e($f['nombre_arrendatario']) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
