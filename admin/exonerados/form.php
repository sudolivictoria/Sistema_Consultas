<?php
/*
   -------------------------------------------
 * EXONERADOS - FORMULARIO (crear y editar)
 * ---------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$errores = [];
$exo = ['dui' => '', 'nombre' => '', 'comunidad' => ''];

if ($id) {
    //----solo las 3 columnas que necesitamos
    $fila = buscar_registro($conexion, 'exonerados_apulo', 'dui, nombre, comunidad', $id);
    if (!$fila) {
        flash('El registro no existe.', 'error');
        redirigir('exonerados/index.php');
    }
    $exo = $fila;
}

//-----procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    $exo = [
        'dui'       => vacio_a_null($_POST['dui'] ?? ''),
        'nombre'    => trim($_POST['nombre'] ?? ''),
        'comunidad' => vacio_a_null($_POST['comunidad'] ?? ''),
    ];

    //----el nombre es obligatorio; el DUI y la comunidad pueden quedar vacíos ("Indefinido")
    if ($exo['nombre'] === '') $errores[] = 'El nombre es obligatorio.';


    //---------valida el dui
    if ($exo['dui'] !== null && !preg_match('/^\d{8}-?\d$/', $exo['dui'])) {
        $errores[] = 'El DUI debe tener el formato 00000000-0.';
    }

    if (!$errores) {
        try {
            guardar_registro($conexion, 'exonerados_apulo', $exo, $id);
            flash($id ? 'El registro se actualizó correctamente.' : 'El registro se creó correctamente.');
            redirigir('exonerados/index.php');
        } catch (mysqli_sql_exception $ex) {
            //----1062 = "valor duplicado" en una columna UNIQUE (el DUI). Cualquier otro error se deja pasar.
            if ($ex->getCode() !== 1062) throw $ex;
            $errores[] = 'Ya existe un registro con ese DUI.';
        }
    }
}

//----listado de comunidades existentes, para sugerirlas en el formulario
$comunidades = $conexion->query("SELECT DISTINCT comunidad FROM exonerados_apulo WHERE comunidad <> '' ORDER BY comunidad")->fetch_all();

//----listado de fondo mas PANEL DEL FORMULARIO
$titulo     = $id ? 'Editar exonerado' : 'Nuevo exonerado';
$seccion    = 'exonerados';
$tituloForm = $titulo;

require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>

<div class="grupo">
    <label for="dui">DUI <span class="opcional">(opcional)</span></label>
    <input id="dui" name="dui" type="text" maxlength="10" placeholder="00000000-0" class="entrada" value="<?= e($exo['dui']) ?>">
    <span class="ayuda">8 dígitos, guion y dígito verificador.</span>
</div>

<div class="grupo">
    <label for="nombre">Nombre completo</label>
    <input id="nombre" name="nombre" type="text" maxlength="150" required class="entrada" value="<?= e($exo['nombre']) ?>">
</div>

<div class="grupo">
    <label for="comunidad">Comunidad <span class="opcional">(opcional)</span></label>
    <input id="comunidad" name="comunidad" type="text" maxlength="255" list="lista-comunidades" placeholder="Seleccione o escriba una comunidad" class="entrada" value="<?= e($exo['comunidad']) ?>">
    <datalist id="lista-comunidades">
        <?php foreach ($comunidades as [$comunidad]): ?>
            <option value="<?= e($comunidad) ?>"></option>
        <?php endforeach; ?>
    </datalist>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
