<?php
/*
 * ============================================================================
 * BOOTSTRAP: el "arranque" de todo el panel /admin
 * ============================================================================
 * Todas las páginas del admin incluyen este archivo primero (directo o a
 * través de auth.php). Aquí se hace lo que TODAS necesitan:
 *   1. Iniciar la sesión
 *   2. Conectarse a la base de datos
 *   3. Definir funciones de ayuda que se usan en todos lados
 */

//----session_start() le dice a PHP: "voy a usar $_SESSION".
//---$_SESSION es un arreglo que se guarda en el SERVIDOR y sobrevive entre paginas.
//---El navegador solo guarda una cookie con el id de la sesión; estas opciones la protegen:
session_start([
    'cookie_httponly' => true,    //----JavaScript no puede leer la cookie (si alguien inyecta un script, no la roba)
    'cookie_samesite' => 'Lax',   //----otro sitio web no puede enviar formularios usando la sesión de alguien
    'use_strict_mode' => true,    //----PHP rechaza ids de sesión que él no creó
]);

//__DIR__ evita errores de rutas
require_once __DIR__ . '/../../conexion.php';       
require_once __DIR__ . '/../../inc/funciones.php'; 
require_once __DIR__ . '/../../inc/iconos.php';     


/*
 * ---------------------------------------------------------------------------
 * ----------------------URLs base del panel----------------------------------
 * ---------------------------------------------------------------------------
 * SITE_URL (la URL base de todo el sitio) está en inc/funciones.php
 */
define('ADMIN_URL', SITE_URL . '/admin');

//------URL del PDF de normativa interna (la ruta en disco, NORMATIVA_PDF_RUTA, está en inc/funciones.php).
//------Lleva version() para que, al reemplazar el PDF, el navegador no muestre el anterior.
define('NORMATIVA_PDF_URL', SITE_URL . '/' . version('uploads/normativa-interna.pdf'));


/*
 * ---------------------------------------------------------------------------
 * ---------------------FUNCIONES ADMIN---------------------------------------
 * ---------------------------------------------------------------------------
 */

//----redirigir() = redirige a otra página del admin y termina la ejecución.
function redirigir($ruta)
{
    header('Location: ' . ADMIN_URL . '/' . ltrim($ruta, '/'));
    exit;
}


/*
 * ---------------------------------------------------------------------------
 * ---------------------FUNCIONES DE BASE DE DATOS----------------------------
 * ---------------------------------------------------------------------------
 * Todos los módulos (convenios, exonerados, arrendatarios, cláusulas) hacen lo
 * mismo: buscar un registro por id, guardarlo (INSERT o UPDATE) y eliminarlo.
 * En vez de repetir ese código en cada archivo, está una sola vez aquí.
 * OJO: $tabla y los nombres de columnas los escribe el programador en el código,
 * nunca vienen del formulario. Los VALORES siempre van con "?" (consulta preparada).
 */

//----devuelve la fila con ese id como arreglo, o null si no existe
function buscar_registro($conexion, $tabla, $columnas, $id)
{
    $stmt = $conexion->prepare("SELECT $columnas FROM $tabla WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->get_result()->fetch_assoc();
}

/*
 * guardar_registro(): crea o actualiza un registro
 *   $datos = ['referencia' => 'ISTU01', 'institucion' => '...']   (columna => valor)
 *   $id    = 0 -> INSERT (nuevo)  |  $id = 5 -> UPDATE del registro 5
 */
function guardar_registro($conexion, $tabla, array $datos, $id)
{
    //----arma "referencia = ?, institucion = ?, ..." a partir de las claves del arreglo
    $campos  = implode(', ', array_map(fn($columna) => "$columna = ?", array_keys($datos)));
    $valores = array_values($datos);

    //----"INSERT ... SET campo = ?" es una forma de MySQL que permite usar la misma
    //----lista de campos para INSERT y para UPDATE
    if ($id) {
        $valores[] = $id;   //----el último ? es el del WHERE id = ?
        $sql = "UPDATE $tabla SET $campos WHERE id = ?";
    } else {
        $sql = "INSERT INTO $tabla SET $campos";
    }

    //----execute($valores) reemplaza cada ? en orden
    $conexion->prepare($sql)->execute($valores);
}

//----elimina el registro con ese id. Devuelve true si se borró algo.
function eliminar_registro($conexion, $tabla, $id)
{
    $stmt = $conexion->prepare("DELETE FROM $tabla WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->affected_rows > 0;
}

/*
 * eliminar_y_volver(): todo lo que hace un eliminar.php
 * -----------------------------------------------------
 * Solo acepta POST con token CSRF, borra el registro, deja el mensaje y regresa al listado. Así cada eliminar.php queda en una sola línea.
 */
function eliminar_y_volver($conexion, $tabla, $listado, $mensajeOk, $mensajeNoExiste)
{
    //----si abren eliminar.php directamente en el navegador, vuelve al listado
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir($listado);
    }
    csrf_verificar();   //----confirma que el formulario salió de nuestro listado

    if (eliminar_registro($conexion, $tabla, (int) ($_POST['id'] ?? 0))) {
        flash($mensajeOk, 'eliminado');
    } else {
        flash($mensajeNoExiste, 'error');
    }
    redirigir($listado);
}


/*
 * Protección CSRF
 * Cada sesión tiene un código secreto aleatorio (token)
 */
function csrf_token()
{
    //-----se genera una sola vez por sesión. random_bytes = aleatorio seguro.
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

//----Devuelve el <input hidden> que se pega dentro de cada <form method="post">
function csrf_campo()
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

//-----Verifica que el token enviado por POST sea igual al de la sesión. Si no, termina la ejecución.
function csrf_verificar()
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Solicitud inválida. Recargue la página e intente de nuevo.');
    }
}


/*
 *-------------------------------------------
 * Mensajes "flash"
 * ------------------------------------------
 * Después de guardar redirigimos al listado
 */

//----flash() = guarda o devuelve un mensaje flash en la sesión.
function flash($mensaje = null, $tipo = 'ok')
{
    if ($mensaje !== null) {
        $_SESSION['flash'] = [$mensaje, $tipo];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

//----limpia un valor: si es null o vacío, devuelve null; si tiene algo, lo devuelve tal cual.
function vacio_a_null($valor)
{
    $valor = trim($valor ?? '');
    return $valor === '' ? null : $valor;
}

//-----devuelve true si la fecha es nula o válida (YYYY-MM-DD)
function fecha_valida($fecha)
{
    if ($fecha === null) return true;
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d && $d->format('Y-m-d') === $fecha;
}

//-----select_si_no() = genera un <select> con las opciones SI y NO, marcando la que corresponda.
function select_si_no($nombre, $valor, $textoSi = 'SI', $textoNo = 'NO')
{
    $valor = si_no($valor);
    $html  = '<select name="' . e($nombre) . '" id="' . e($nombre) . '" class="entrada">';
    foreach (['SI' => $textoSi, 'NO' => $textoNo] as $op => $texto) {
        $html .= '<option value="' . $op . '"' . ($valor === $op ? ' selected' : '') . '>' . e($texto) . '</option>';
    }
    return $html . '</select>';
}

//-----inicial() devuelve la primera letra de un nombre, en mayúscula.
function inicial($nombre)
{
    return mb_strtoupper(mb_substr(trim($nombre ?? ''), 0, 1));
}
