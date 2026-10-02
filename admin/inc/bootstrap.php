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
//---$_SESSION es un arreglo que se guarda en el SERVIDOR y sobrevive entre paginas
session_start();

// __DIR__ evita errores de rutas
require_once __DIR__ . '/../../conexion.php';       
require_once __DIR__ . '/../../inc/funciones.php'; 
require_once __DIR__ . '/../../inc/iconos.php';     


/*
 * ---------------------------------------------------------------------------
 * ----------------------URLs base del sitio----------------------------------
 * ---------------------------------------------------------------------------
 */
$raizWeb   = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$raizSitio = str_replace('\\', '/', dirname(__DIR__, 2));  

//------define() crea una CONSTANTE: un valor fijo que se puede usar en cualquier archivo y función sin pasarlo como parámetro.
define('SITE_URL', stripos($raizSitio, $raizWeb) === 0 ? substr($raizSitio, strlen($raizWeb)) : '');
define('ADMIN_URL', SITE_URL . '/admin');

//------URL del PDF de política interna (la ruta en disco, POLITICA_PDF_RUTA, está en inc/funciones.php).
//------Lleva version() para que, al reemplazar el PDF, el navegador no muestre el anterior.
define('POLITICA_PDF_URL', SITE_URL . '/' . version('uploads/politica-interna.pdf'));


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
 * Mensajes "flash"
 * ----------------
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
