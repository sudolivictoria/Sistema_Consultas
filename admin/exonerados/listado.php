<?php
/*---------------------------
 * EXONERADOS - LISTADO 
 * --------------------------
 */


if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
$resultado = $conexion->query("SELECT id, dui, nombre, comunidad FROM exonerados_apulo ORDER BY nombre");
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <span>Exonerados</span></nav>
        <h1 class="titulo titulo-admin">Administrar <span>exonerados</span></h1>
        <p class="subtitulo">Listado de personas exoneradas en el Turicentro Apulo.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nuevo exonerado</a>
</div>

<section class="panel">
    <div class="panel-herramientas">
        <div class="campo-busqueda campo-busqueda-chico">
            <label for="buscarTabla" class="solo-lectores">Buscar por nombre o DUI</label>
            <?= icono('buscar', 18, 2.2) ?>
            <input id="buscarTabla" type="search" placeholder="Buscar por nombre o DUI…" autocomplete="off">
        </div>
        <span class="contador"><?= $resultado->num_rows ?> registros</span>
    </div>

    <table class="tabla tabla-admin">
        <thead>
            <tr>
                <th scope="col">DUI</th>
                <th scope="col">Nombre completo</th>
                <th scope="col">Comunidad</th>
                <th scope="col" class="derecha">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($f = $resultado->fetch_assoc()): ?>
                <tr>
                    <!--data-search: se encuentra con guion o sin guion-->
                    <td class="mono" data-label="DUI" data-search="<?= e($f['dui'] . ' ' . str_replace('-', '', $f['dui'] ?? '')) ?>"><?= e($f['dui']) ?></td>
                    <td class="fuerte" data-label="Nombre"><?= e($f['nombre']) ?></td>
                    <td data-label="Comunidad">
                        <?php if (!empty($f['comunidad'])): ?>
                            <span class="pastilla"><?= e($f['comunidad']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="acciones" data-label="Acciones">
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar a <?= e($f['nombre']) ?>"><?= icono('editar', 16) ?></a>
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar este exonerado?" data-nombre="<?= e($f['nombre']) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar a <?= e($f['nombre']) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
