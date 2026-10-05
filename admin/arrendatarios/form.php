<?php
/*
   ----------------------------------------------
 * ARRENDATARIOS - FORMULARIO (crear y editar)
 * ----------------------------------------------
 */

//---require auth.php para que solo usuarios logueados puedan acceder a este archivo
require_once __DIR__ . '/../inc/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$errores = [];

//----VALORES INICIALES
$arr = ['nombre_arrendatario' => '', 'local_comercial' => '', 'venta_autorizada' => ''];

//---si viene un id (editar), buscamos el arrendatario en la base de datos
if ($id) {
    //---buscar_registro() está en admin/inc/bootstrap.php. Recibe la conexión, la tabla, las columnas a traer y el id.
    //---Devuelve un arreglo con los datos, o null si no existe.
    $fila = buscar_registro($conexion, 'arrendatarios', 'nombre_arrendatario, local_comercial, venta_autorizada', $id);
    //---si no existe (ej. lo borraron en otra pestaña), volvemos al listado con un mensaje de error
    if (!$fila) {
        //---flash() guarda el mensaje en la sesión para mostrarlo en la siguiente página
        flash('El arrendatario no existe.', 'error');
        //---redirigir() está en admin/inc/bootstrap.php. Recibe la ruta dentro de /admin e incluye exit: aquí se detiene todo.
        redirigir('arrendatarios/index.php');
    }
    //---si existe, lo guardamos en $arr para mostrarlo en el formulario
    $arr = $fila;
}

//------PROCESAR EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //---verificamos el token CSRF (que el formulario salió de nuestra página).
    //---Si no es válido, se detiene todo con un error 400 "Solicitud inválida".
    csrf_verificar();
    //---recibimos los datos del formulario y los limpiamos (trim quita espacios al inicio y al final).
    //---Las claves son los nombres de las columnas de la tabla: guardar_registro() arma el SQL con ellas.
    //---el nombre es obligatorio. El local y la venta son OPCIONALES
    $arr = [
        'nombre_arrendatario' => trim($_POST['nombre_arrendatario'] ?? ''),
        'local_comercial'     => vacio_a_null($_POST['local_comercial'] ?? ''),
        'venta_autorizada'    => vacio_a_null(implode(', ', lista_desde_comas($_POST['venta_autorizada'] ?? ''))),
    ];
    if ($arr['nombre_arrendatario'] === '') $errores[] = 'El nombre del arrendatario es obligatorio.';
    //---si no hay errores, guardamos en la base de datos
    if (!$errores) {
        //---guardar_registro() está en admin/inc/bootstrap.php. Recibe la conexión, la tabla,
        //---el arreglo de datos (columna => valor) y el id: 0 = crear (INSERT), mayor que 0 = editar (UPDATE).
        guardar_registro($conexion, 'arrendatarios', $arr, $id);
        //---patrón POST -> redirigir -> GET: si se presiona F5 en el listado, no se vuelve a guardar
        flash($id ? 'El arrendatario se actualizó correctamente.' : 'El arrendatario se creó correctamente.');
        redirigir('arrendatarios/index.php');
    }
    //---si hubo errores NO redirigimos: se muestra el formulario otra vez con lo que se escribió y los errores
}

//--------LISTADO DE FONDO + FORMULARIO LATERAL
$titulo     = $id ? 'Editar arrendatario' : 'Nuevo arrendatario';
$seccion    = 'arrendatarios';
$tituloForm = $titulo;
//---header.php: el <head>, el menú lateral y el inicio de la página
require __DIR__ . '/../inc/header.php';
//---listado.php: la tabla de arrendatarios que queda de fondo
require __DIR__ . '/listado.php';
//---drawer_inicio.php: abre el panel lateral y el <form> (con el token CSRF y la lista de errores)
require __DIR__ . '/../inc/drawer_inicio.php';
?>
<!--CAMPOS DEL FORMULARIO (crear o editar un arrendatario)-->
<!--El nombre es obligatorio; el local y la venta son opcionales (vacíos se muestran como "Indefinido")-->
<div class="grupo">
    <label for="nombre_arrendatario">Nombre del arrendatario</label>
    <input id="nombre_arrendatario" name="nombre_arrendatario" type="text" maxlength="150" required class="entrada" value="<?= e($arr['nombre_arrendatario']) ?>">
</div>

<div class="grupo">
    <label for="local_comercial">Local comercial <span class="opcional">(opcional)</span></label>
    <input id="local_comercial" name="local_comercial" type="text" maxlength="150" placeholder="Nombre del local" class="entrada" value="<?= e($arr['local_comercial']) ?>">
</div>

<div class="grupo">
    <label for="ventaEntrada">Venta autorizada <span class="opcional">(opcional)</span></label>
    <!--data-campo="venta_autorizada" le dice a admin.js en qué input hidden debe guardar la lista separada por comas.-->
    <div class="etiquetas" data-campo="venta_autorizada">
        <!--La venta autorizada se guarda como "Agua, Dulces": se separa por las comas y cada producto se muestra como etiqueta-->
        <!--Al agregar o quitar etiquetas, admin.js actualiza el input hidden de abajo, que es lo que se envía a PHP-->
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
//---drawer_fin.php: botones Cancelar / Guardar y cierre del formulario
require __DIR__ . '/../inc/drawer_fin.php';
//---footer.php: cierre de la página y los scripts
require __DIR__ . '/../inc/footer.php';
