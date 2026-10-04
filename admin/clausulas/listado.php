<?php
/*
---------------------------------------------------------
 *         CLÁUSULAS CONTRACTUALES - LISTADO 
 * -------------------------------------------------------
 */


if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
//----------ordenar por el numero de clausula
$resultado = $conexion->query("SELECT id, numero, titulo FROM clausulas ORDER BY numero");
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <a href="../arrendatarios/index.php">Arrendatarios</a> / <span>Cláusulas</span></nav>
        <h1 class="titulo titulo-admin">Cláusulas <span>contractuales</span></h1>
        <p class="subtitulo">Se muestran en la consulta pública de arrendatarios, en el orden de su número.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nueva cláusula</a>
</div>

<section class="panel">
    <div class="panel-herramientas">
        <div class="campo-busqueda campo-busqueda-chico">
            <label for="buscarTabla" class="solo-lectores">Buscar cláusula</label>
            <?= icono('buscar', 18, 2.2) ?>
            <input id="buscarTabla" type="search" placeholder="Buscar cláusula…" autocomplete="off">
        </div>
        <span class="contador"><?= $resultado->num_rows ?> cláusulas</span>
    </div>

    <table class="tabla tabla-admin">
        <thead>
            <tr>
                <th scope="col" class="col-numero">N.º</th>
                <th scope="col">Título</th>
                <th scope="col" class="derecha">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!--cada fila de la tabla es una cláusula, con botones para editar o eliminar-->
            <?php while ($f = $resultado->fetch_assoc()): ?>
                <tr>
                    <td data-label="N.º"><span class="ref"><?= e($f['numero']) ?></span></td>
                    <td class="fuerte" data-label="Título"><?= e($f['titulo']) ?></td>
                    <td class="acciones" data-label="Acciones">
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar cláusula <?= e($f['numero']) ?>"><?= icono('editar', 16) ?></a>
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar esta cláusula?" data-nombre="la cláusula <?= e($f['numero']) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar cláusula <?= e($f['numero']) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
