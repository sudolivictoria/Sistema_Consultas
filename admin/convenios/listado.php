<?php
/*
 * ============================================================================
 * -----------------------------CONVENIOS - LISTADO----------------------------- 
 * ============================================================================
 */

if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}
$resultado = $conexion->query("SELECT id, referencia, institucion, vigencia, suscripcion, vencimiento, plazo FROM convenios ORDER BY referencia");
?>

<div class="pagina-cabecera">
    <div class="pagina-titulos">
        <nav class="ruta" aria-label="Ruta">Administración / <span>Convenios</span></nav>
        <h1 class="titulo titulo-admin">Administrar <span>convenios</span></h1>
        <p class="subtitulo">Cree, edite o elimine los convenios que se muestran en la consulta pública.</p>
    </div>
    <a href="form.php" class="btn btn-oscuro"><?= icono('mas', 18, 2.4) ?> Nuevo convenio</a>
</div>

<section class="panel">
    <div class="panel-herramientas">
        <div class="campo-busqueda campo-busqueda-chico">
            <label for="buscarTabla" class="solo-lectores">Buscar por referencia o institución</label>
            <?= icono('buscar', 18, 2.2) ?>
            <input id="buscarTabla" type="search" placeholder="Buscar por referencia o institución…" autocomplete="off">
        </div>
        <span class="contador"><?= $resultado->num_rows ?> registros</span>
    </div>

    <!--TABLA DE CONVENIOS-->
    <table class="tabla tabla-admin">
        <thead>
            <tr>
                <th scope="col">Ref.</th>
                <th scope="col">Institución</th>
                <th scope="col">Estado</th>
                <th scope="col">Suscripción</th>
                <th scope="col">Vencimiento</th>
                <th scope="col">Plazo</th>
                <th scope="col" class="derecha">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($f = $resultado->fetch_assoc()):
            ?>
                <tr>
                    <!--La forma corta de PHP "imprimir" equivale a echo. Siempre con e() para escapar-->
                    <td data-label="Ref."><span class="ref"><?= e($f['referencia']) ?></span></td>
                    <td class="fuerte" data-label="Institución"><?= e($f['institucion']) ?></td>
                    <td data-label="Estado"><?= badge_vigencia($f['vigencia']) ?></td>
                    <!--data-order: DataTables ordena por la fecha real-->
                    <td class="numeros" data-label="Suscripción" data-order="<?= e($f['suscripcion']) ?>"><?= fecha_mostrar($f['suscripcion']) ?></td>
                    <td class="numeros" data-label="Vencimiento" data-order="<?= e($f['vencimiento']) ?>"><?= fecha_mostrar($f['vencimiento']) ?></td>
                    <td data-label="Plazo"><?= e($f['plazo'] ?: 'Indefinido') ?></td>
                    <td class="acciones" data-label="Acciones">
                        <!--Editar: un simple enlace con el id en la URL-->
                        <a href="form.php?id=<?= $f['id'] ?>" class="btn-icono btn-editar" aria-label="Editar <?= e($f['referencia']) ?>"><?= icono('editar', 16) ?></a>
                        <!--Eliminar: un formulario con POST, para que no se pueda borrar con un simple enlace-->
                        <form method="post" action="eliminar.php" class="form-eliminar" data-titulo="¿Eliminar este convenio?" data-nombre="<?= e($f['referencia']) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn-icono btn-borrar" aria-label="Eliminar <?= e($f['referencia']) ?>"><?= icono('eliminar', 16) ?></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
