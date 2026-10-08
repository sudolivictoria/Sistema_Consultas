<?php
/*
   ----------------------------------------------
 * PARQUES - FORMULARIO (crear y editar)
 * ----------------------------------------------
 * La clave se guarda como HASH (password_hash), igual que las contraseñas de los usuarios:
 * después de guardarla NO se puede volver a ver, solo reemplazar por otra.
 */
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../../inc/acceso_parque.php';   //----parque_por_clave(): para no repetir claves entre parques

$id = (int) ($_GET['id'] ?? 0);
$errores = [];

//----VALORES INICIALES
$par = ['nombre' => ''];
$tieneClave = false;   //----si el parque ya tiene clave (al editar)

if ($id) {
    $fila = buscar_registro($conexion, 'parques', 'nombre, clave', $id);
    if (!$fila) {
        flash('El parque no existe.', 'error');
        redirigir('parques/index.php');
    }
    $par = ['nombre' => $fila['nombre']];
    $tieneClave = $fila['clave'] !== null;
}

//------PROCESAR EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    $par   = ['nombre' => trim($_POST['nombre'] ?? '')];
    $clave = trim($_POST['clave'] ?? '');

    if ($par['nombre'] === '') {
        $errores[] = 'El nombre del parque es obligatorio.';
    } else {
        //----el nombre no se puede repetir (la base lo exige: UNIQUE). Se revisa antes para dar un mensaje claro.
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM parques WHERE nombre = ? AND id <> ?");
        $stmt->execute([$par['nombre'], $id]);
        if ($stmt->get_result()->fetch_row()[0] > 0) $errores[] = 'Ya existe un parque con ese nombre.';
    }

    //----la clave es obligatoria al crear; al editar, vacía = se queda la que tenía
    if ($clave === '') {
        if (!$tieneClave) $errores[] = 'Escriba la clave del parque: sin ella nadie puede ver sus arrendatarios.';
    } elseif (mb_strlen($clave) < 6) {
        $errores[] = 'La clave debe tener al menos 6 caracteres.';
    } elseif (parque_por_clave($conexion, $clave, $id)) {
        //----si dos parques tuvieran la misma clave, no se sabría a cuál entrar
        $errores[] = 'Esa clave ya la usa otro parque. Escriba una distinta.';
    }

    if (!$errores) {
        //----solo si se escribió una clave nueva, se guarda su hash
        if ($clave !== '') $par['clave'] = password_hash($clave, PASSWORD_DEFAULT);

        guardar_registro($conexion, 'parques', $par, $id);

        if (!$id) {
            flash('El parque se creó correctamente.');
        } elseif ($clave !== '' && $tieneClave) {
            flash('Se guardó la nueva clave. Quienes usaban la anterior deberán escribir la nueva.');
        } else {
            flash('El parque se actualizó correctamente.');
        }
        redirigir('parques/index.php');
    }
}

//--------LISTADO DE FONDO + FORMULARIO LATERAL
$titulo     = $id ? 'Editar parque' : 'Nuevo parque';
$seccion    = 'parques';
$tituloForm = $titulo;
require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>
<div class="grupo">
    <label for="nombre">Nombre del parque</label>
    <input id="nombre" name="nombre" type="text" maxlength="150" required class="entrada" placeholder="Ej.: Parque Natural Balboa" value="<?= e($par['nombre']) ?>">
</div>

<div class="grupo">
    <label for="clave">
        <?= $tieneClave ? 'Nueva clave <span class="opcional">(opcional)</span>' : 'Clave del parque' ?>
    </label>
    <!--type="text" (no password): quien administra necesita ver lo que escribe para luego entregarla.
        Nunca se rellena con la clave actual: solo existe su hash.-->
    <input id="clave" name="clave" type="text" maxlength="100" class="entrada" autocomplete="off" spellcheck="false" <?= $tieneClave ? '' : 'required' ?>>
    <span class="ayuda">
        <?php if ($tieneClave): ?>
            Déjela vacía para mantener la clave actual. Si escribe una nueva, anótela: después no se puede volver a ver.
        <?php else: ?>
            Mínimo 6 caracteres. Anótela antes de guardar: por seguridad, después no se puede volver a ver.
        <?php endif; ?>
    </span>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
