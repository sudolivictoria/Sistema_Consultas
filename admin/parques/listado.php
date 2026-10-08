<?php
/*
 *-------------------------------------------
 * PARQUES - LISTADO
 * Lo usan index.php y form.php.
 * ------------------------------------------
 */

//---si alguien abre listado.php directamente (sin pasar por index.php y auth.php), lo mandamos al index
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
//---cada parque con cuántos arrendatarios tiene y si ya tiene clave.
//---La clave NO se trae: es un hash y no se puede volver a leer, solo se pregunta si existe.
$resultado = $conexion->query(
    "SELECT p.id, p.nombre, p.clave IS NOT NULL AS tiene_clave, COUNT(a.id) AS total
     FROM parques p
     LEFT JOIN arrendatarios a ON a.parque_id = p.id
     GROUP BY p.id, p.nombre, p.clave
     ORDER BY p.nombre"
);
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <span>Parques</span></nav>
        <h1 class="titulo titulo-admin">Administrar <span>parques</span></h1>
        <p class="subtitulo">Cada parque entra a la consulta pública de arrendatarios con su propia clave y solo ve sus arrendatarios.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nuevo parque</a>
</div>

<section class="panel">
    <div class="panel-herramientas">
        <div class="campo-busqueda campo-busqueda-chico">
            <label for="buscarTabla" class="solo-lectores">Buscar parque</label>
            <?= icono('buscar', 18, 2.2) ?>
            <input id="buscarTabla" type="search" placeholder="Buscar parque…" autocomplete="off">
        </div>
        <span class="contador"><?= $resultado->num_rows ?> parques</span>
    </div>

    <table class="tabla tabla-admin">
        <thead>
            <tr>
                <th scope="col">Parque</th>
                <th scope="col">Arrendatarios</th>
                <th scope="col">Clave</th>
                <th scope="col" class="derecha">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($f = $resultado->fetch_assoc()): ?>
                <?php $nombre = $f['nombre']; ?>
                <tr>
                    <td class="fuerte" data-label="Parque"><?= e($nombre) ?></td>
                    <td class="numeros" data-label="Arrendatarios"><?= $f['total'] ?></td>
                    <td data-label="Clave">
                        <!--sin clave, nadie puede ver los arrendatarios de ese parque en la consulta pública-->
                        <?php if ($f['tiene_clave']): ?>
                            <span class="estado estado-vigente">Asignada</span>
                        <?php else: ?>
                            <span class="estado estado-vencido">Sin clave</span>
                        <?php endif; ?>
                    </td>
                    <td class="acciones" data-label="Acciones">
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar <?= e($nombre) ?>"><?= icono('editar', 16) ?></a>
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar este parque?" data-nombre="<?= e($nombre) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar <?= e($nombre) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
