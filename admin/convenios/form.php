<?php
/*
 * ============================================================================
 * CONVENIOS - FORMULARIO  (la "C" y la "U" de CRUD: Create / Update)
 * ============================================================================
 * Un solo archivo sirve para crear y para editar:
 *   form.php        -> $id = 0  -> formulario vacío  -> al guardar hace INSERT
 *   form.php?id=5   -> $id = 5  -> carga el convenio -> al guardar hace UPDATE
 * 
 *   1. Preparar datos (vacíos o cargados de la base)
 *   2. Si llegó un POST: leer, validar y guardar
 *   3. Mostrar el listado de fondo y, encima, el panel con el formulario
 */
require_once __DIR__ . '/../inc/auth.php';

//---protección extra: el id siempre será un número.
$id = (int) ($_GET['id'] ?? 0);
$errores = [];

/*
 * siguiente_referencia(): sugiere la referencia para un convenio nuevo
 * ---------------------------------------------------------------------
 * Las referencias son ISTU01, ISTU02... Se busca el número MÁS ALTO (no el último registrado) 
 */
function siguiente_referencia($conexion)
{
    $mayor = $conexion->query(
        "SELECT MAX(CAST(SUBSTRING(referencia, 5) AS UNSIGNED)) FROM convenios WHERE referencia REGEXP '^ISTU[0-9]+$'"
    )->fetch_row()[0];

    //----si todavía no hay ninguna, $mayor es null y (int) null = 0 -> ISTU01
    return sprintf('ISTU%02d', (int) $mayor + 1);
}

// ---------------------------------------------------------------------------
//                          valores iniciales
// ---------------------------------------------------------------------------
//------Para un convenio nuevo, todos los campos empiezan vacíos (menos la referencia, que se sugiere).
//------Tener el arreglo completo evita errores de "índice no definido" en el HTML.
$conv = [
    'referencia' => $id ? '' : siguiente_referencia($conexion), 'institucion' => '', 'vigencia' => 'SI', 'descripcion' => '',
    'suscripcion' => '', 'plazo' => '', 'vencimiento' => '', 'comentario' => '',
    'exoneracion_desc' => '', 'promocion_desc' => '',
];

//----si viene un id, cargamos ese convenio de la base para editarlo
if ($id) {
    $fila = buscar_registro($conexion, 'convenios', '*', $id);

    //----si no existe (ej. lo borraron en otra pestaña), volvemos al listado
    if (!$fila) {
        flash('El convenio no existe.', 'error');
        redirigir('convenios/index.php');
    }
    $conv = $fila;

    //---datos viejos: si el campo dice NO pero quedó texto guardado de antes, no lo mostramos. Si lo mostráramos, al guardar se volvería SI sin querer.
    if (si_no($conv['exoneracion']) === 'NO') $conv['exoneracion_desc'] = '';
    if (si_no($conv['promocion']) === 'NO')   $conv['promocion_desc'] = '';
}

// ---------------------------------------------------------------------------
//                          procesar el formulario enviado
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    //---leemos cada campo de $_POST y lo limpiamos.
    $conv = [
        'referencia'       => trim($_POST['referencia'] ?? ''),
        'institucion'      => trim($_POST['institucion'] ?? ''),
        'vigencia'         => si_no($_POST['vigencia'] ?? ''),
        'descripcion'      => vacio_a_null($_POST['descripcion'] ?? ''),
        'suscripcion'      => vacio_a_null($_POST['suscripcion'] ?? ''),
        'plazo'            => vacio_a_null($_POST['plazo'] ?? ''),
        'vencimiento'      => vacio_a_null($_POST['vencimiento'] ?? ''),
        'comentario'       => vacio_a_null($_POST['comentario'] ?? ''),
        'exoneracion_desc' => vacio_a_null($_POST['exoneracion_desc'] ?? ''),
        'promocion_desc'   => vacio_a_null($_POST['promocion_desc'] ?? ''),
    ];

    //---Casilla "Vencimiento indefinido": si viene marcada, no hay fecha de vencimiento.
    //----(Una casilla sin marcar no se envía, por eso se pregunta con isset.)
    if (isset($_POST['indefinido'])) {
        $conv['vencimiento'] = null;
    }

    /*
     * EXONERACIÓN Y PROMOCIÓN: el SI / NO se calcula solo
     * -----------------------------------------------------
     * El formulario ya no tiene un select SI/NO. Solo hay un cuadro de texto:
     *   - si escribieron algo  -> la columna exoneracion se guarda como 'SI'
     *   - si quedó vacío       -> se guarda como 'NO' (y el texto como NULL)
     */
    $conv['exoneracion'] = $conv['exoneracion_desc'] !== null ? 'SI' : 'NO';
    $conv['promocion']   = $conv['promocion_desc'] !== null ? 'SI' : 'NO';

    //-----VALIDACIÓN: el navegador ya revisa "required" y "maxlength", pero eso se
    if ($conv['referencia'] === '') $errores[] = 'La referencia es obligatoria.';
    if (mb_strlen($conv['referencia']) > 20) $errores[] = 'La referencia admite máximo 20 caracteres.';
    if ($conv['institucion'] === '') $errores[] = 'La institución es obligatoria.';
    if (!fecha_valida($conv['suscripcion'])) $errores[] = 'La fecha de suscripción no es válida.';
    if (!fecha_valida($conv['vencimiento'])) $errores[] = 'La fecha de vencimiento no es válida.';

    //-----Solo guardamos si no hubo ningún error.
    //-----Las claves de $conv son los nombres de las columnas: guardar_registro() arma el SQL con ellas.
    if (!$errores) {
        try {
            guardar_registro($conexion, 'convenios', $conv, $id);

            /*
             * Patrón POST -> Redirigir -> GET
             * Después de guardar redirigimos al listado en vez de mostrar la página
             */
            flash($id ? 'El convenio se actualizó correctamente.' : 'El convenio se creó correctamente.');
            redirigir('convenios/index.php');
        } catch (mysqli_sql_exception $ex) {
            //----1062 = valor duplicado en una columna UNIQUE (la referencia). Cualquier otro error se deja pasar.
            if ($ex->getCode() !== 1062) throw $ex;
            $errores[] = 'Ya existe un convenio con la referencia ' . $conv['referencia'] . '.';
        }
    }
}

// ---------------------------------------------------------------------------
//      mostrar el listado de fondo y el formulario en el panel lateral
// ---------------------------------------------------------------------------
$titulo     = $id ? 'Editar convenio' : 'Nuevo convenio';
$seccion    = 'convenios';
$tituloForm = $titulo;

//---sin fecha de vencimiento = la casilla "indefinido" aparece marcada
$sinVencimiento = empty($conv['vencimiento']) || $conv['vencimiento'] === '0000-00-00';

require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>

<!--Cada name="..." es la clave con la que llega en $_POST-->
<div class="dos-columnas">
    <div class="grupo">
        <label for="referencia">Referencia</label>
        <input id="referencia" name="referencia" type="text" maxlength="20" required placeholder="ISTU00" class="entrada" value="<?= e($conv['referencia']) ?>">
    </div>
    <div class="grupo">
        <label for="vigencia">Estado</label>
        <!--Se guarda SI / NO, pero en pantalla se lee "Vigente" / "No vigente"-->
        <?= select_si_no('vigencia', $conv['vigencia'], 'Vigente', 'No vigente') ?>
    </div>
</div>

<div class="grupo">
    <label for="institucion">Institución</label>
    <!--Un textarea no tiene value="": el contenido va entre las etiquetas-->
    <textarea id="institucion" name="institucion" rows="2" maxlength="255" required placeholder="Nombre completo de la institución" class="entrada"><?= e($conv['institucion']) ?></textarea>
</div>

<div class="dos-columnas">
    <div class="grupo">
        <label for="suscripcion">Suscripción</label>
        <!--type="date" muestra un calendario y envía la fecha como AAAA-MM-DD-->
        <input id="suscripcion" name="suscripcion" type="date" class="entrada" value="<?= e($conv['suscripcion'] === '0000-00-00' ? '' : $conv['suscripcion']) ?>">
    </div>
    <div class="grupo">
        <label for="vencimiento">Vencimiento</label>
        <input id="vencimiento" name="vencimiento" type="date" class="entrada" value="<?= e($sinVencimiento ? '' : $conv['vencimiento']) ?>">
    </div>
</div>

<!--Al marcarla, admin.js desactiva la fecha de vencimiento-->
<label class="casilla">
    <input type="checkbox" id="indefinido" name="indefinido" value="1" <?= $sinVencimiento ? 'checked' : '' ?>>
    Vencimiento indefinido
</label>

<div class="grupo">
    <label for="plazo">Plazo</label>
    <input id="plazo" name="plazo" type="text" maxlength="100" placeholder="Ej.: 1 año, 2 días, indefinido" class="entrada" value="<?= e($conv['plazo']) ?>">
</div>

<div class="grupo">
    <label for="descripcion">Descripción del convenio</label>
    <textarea id="descripcion" name="descripcion" rows="4" class="entrada"><?= e($conv['descripcion']) ?></textarea>
</div>

<div class="grupo">
    <label for="exoneracion_desc">Exoneración <span class="opcional">(opcional)</span></label>
    <textarea id="exoneracion_desc" name="exoneracion_desc" rows="3" class="entrada"><?= e($conv['exoneracion_desc']) ?></textarea>
    <span class="ayuda">Si queda vacío, no se mostrará en el detalle.</span>
</div>

<div class="grupo">
    <label for="promocion_desc">Promoción <span class="opcional">(opcional)</span></label>
    <textarea id="promocion_desc" name="promocion_desc" rows="3" class="entrada"><?= e($conv['promocion_desc']) ?></textarea>
    <span class="ayuda">Si queda vacío, no se mostrará en el detalle.</span>
</div>

<div class="grupo">
    <label for="comentario">Comentarios y aclaraciones <span class="opcional">(opcional)</span></label>
    <textarea id="comentario" name="comentario" rows="2" class="entrada"><?= e($conv['comentario']) ?></textarea>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
