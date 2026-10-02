<?php
/*
------------------------------------------------
 * ARRENDATARIOS - FORMULARIO (crear y editar)
 * ----------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$errores = [];

//----VALORES INICIALES
$arr = ['nombre_arrendatario' => '', 'local_comercial' => '', 'venta_autorizada' => ''];

if ($id) {
    $stmt = $conexion->prepare("SELECT nombre_arrendatario, local_comercial, venta_autorizada FROM arrendatarios WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    if (!$fila) {
        flash('El arrendatario no existe.', 'error');
        redirigir('arrendatarios/index.php');
    }
    $arr = $fila;
}

//------PROCESAR EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    $arr = [
        'nombre_arrendatario' => trim($_POST['nombre_arrendatario'] ?? ''),
        'local_comercial'     => trim($_POST['local_comercial'] ?? ''),
        'venta_autorizada'    => implode(', ', lista_desde_comas($_POST['venta_autorizada'] ?? '')),
    ];

    if ($arr['nombre_arrendatario'] === '') $errores[] = 'El nombre del arrendatario es obligatorio.';
    if ($arr['local_comercial'] === '') $errores[] = 'El local comercial es obligatorio.';
    if ($arr['venta_autorizada'] === '') $errores[] = 'Agregue al menos un producto o servicio autorizado.';

    if (!$errores) {
        $valores = [$arr['nombre_arrendatario'], $arr['local_comercial'], $arr['venta_autorizada']];
        $campos  = "nombre_arrendatario = ?, local_comercial = ?, venta_autorizada = ?";

        if ($id) {
            $valores[] = $id;
            $conexion->prepare("UPDATE arrendatarios SET $campos WHERE id = ?")->execute($valores);
        } else {
            $conexion->prepare("INSERT INTO arrendatarios SET $campos")->execute($valores);
        }

        flash($id ? 'El arrendatario se actualizó correctamente.' : 'El arrendatario se creó correctamente.');
        redirigir('arrendatarios/index.php');
    }
}

//--------LISTADO + FORMULARIO LATERAL
$titulo     = $id ? 'Editar arrendatario' : 'Nuevo arrendatario';
$seccion    = 'arrendatarios';
$tituloForm = $titulo;

require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>

<div class="grupo">
    <label for="nombre_arrendatario">Nombre del arrendatario</label>
    <input id="nombre_arrendatario" name="nombre_arrendatario" type="text" maxlength="150" required class="entrada" value="<?= e($arr['nombre_arrendatario']) ?>">
</div>

<div class="grupo">
    <label for="local_comercial">Local comercial</label>
    <input id="local_comercial" name="local_comercial" type="text" maxlength="150" required placeholder="Nombre del local" class="entrada" value="<?= e($arr['local_comercial']) ?>">
</div>

<div class="grupo">
    <label for="ventaEntrada">Venta autorizada</label>
    <!--data-campo="venta_autorizada" le dice a admin.js en qué input hidden debe guardar la lista separada por comas.-->
    <div class="etiquetas" data-campo="venta_autorizada">
        <?php foreach (lista_desde_comas($arr['venta_autorizada']) as $producto): ?>
            <span class="etiqueta-item" data-valor="<?= e($producto) ?>"><?= e($producto) ?><button type="button" class="etiqueta-quitar" aria-label="Quitar <?= e($producto) ?>"><?= icono('cerrar', 13, 2.4) ?></button></span>
        <?php endforeach; ?>
        <input id="ventaEntrada" type="text" placeholder="Escriba y presione Enter…" autocomplete="off">
    </div>
    <!--Lo que se envía a PHP-->
    <input type="hidden" id="venta_autorizada" name="venta_autorizada" value="<?= e($arr['venta_autorizada']) ?>">
    <span class="ayuda">Agregue cada producto o servicio por separado.</span>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
