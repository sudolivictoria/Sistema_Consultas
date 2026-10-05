<?php
/*
 * ============================================================================
 * FUNCIONES COMPARTIDAS (sitio público + panel /admin)
 * ============================================================================
 * Las funciones que solo usa el admin (sesión, CSRF, mensajes, base de datos) están en admin/inc/bootstrap.php
 * Las funciones que usa el sitio público (inicio, exonerados, convenios, arrendatarios) están en este archivo
 */

//----------------------URL BASE DEL SITIO----------------------
//--Compara la carpeta del proyecto con la raíz del servidor web (DOCUMENT_ROOT):
//--  proyecto en C:/.../WORK/Sistema_Consultas y raíz en C:/.../WORK -> SITE_URL = '/Sistema_Consultas'
//--  si el proyecto ES la raíz (servidor, virtual host, php -S)        -> SITE_URL = ''
//--define() crea una CONSTANTE: un valor fijo que se puede usar en cualquier archivo y función.
$raizWeb   = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$raizSitio = str_replace('\\', '/', dirname(__DIR__));
define('SITE_URL', stripos($raizSitio, $raizWeb) === 0 ? substr($raizSitio, strlen($raizWeb)) : '');
unset($raizWeb, $raizSitio);

//----------------------RED DE SEGURIDAD PARA ERRORES INESPERADOS----------------------
//--Si algo falla sin que el código lo haya previsto (ej. la base rechaza un dato)
//--  1) el detalle se guarda en el registro de errores de PHP (para revisarlo después)
//--  2) la persona ve un mensaje amable
set_exception_handler(function ($ex) {
    error_log('Error no controlado: ' . $ex->getMessage() . ' en ' . $ex->getFile() . ':' . $ex->getLine());
    http_response_code(500);
    echo '<p style="font-family: sans-serif; padding: 24px">Ocurrió un error inesperado. Intente de nuevo; si el problema continúa, avise a soporte técnico.</p>';
});

//----------------------RUTA DEL PDF DE POLÍTICA INTERNA----------------------
//--se gestiona desde admin y se muestra en el sitio público. Se guarda en uploads/politica-interna.pdf
define('POLITICA_PDF_RUTA', __DIR__ . '/../uploads/politica-interna.pdf');

//----e() = "escapar" Convierte < > " ' & en texto inofensivo y "?? ''" convierte NULL en texto vacío para que no dé error.
function e($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

//------si_no() = devuelve "SI" o "NO" según el valor que se le pase.
function si_no($valor)
{
    return strtoupper(trim($valor ?? '')) === 'SI' ? 'SI' : 'NO';
}

//-----muestra una fecha de la base (2024-08-18) como 18-08-2024 y si no hay fecha, muestra el texto que se indique (por defecto "Indefinido").
function fecha_mostrar($fecha, $siVacia = 'Indefinido')
{
    return (!empty($fecha) && $fecha !== '0000-00-00') ? (new DateTime($fecha))->format('d-m-Y') : $siVacia;
}

//----etiqueta "Vigente" (verde) o "No Vigente" (roja) según la columna vigencia (SI / NO).
function badge_vigencia($vigencia)
{
    return si_no($vigencia) === 'SI'
        ? '<span class="estado estado-vigente">Vigente</span>'
        : '<span class="estado estado-vencido">No vigente</span>';
}

//----texto_o_indefinido() = muestra el texto (escapado) o, si viene vacío o NULL, "Indefinido" en gris.
//----Se usa donde la información puede llegar incompleta (ej. arrendatarios).
function texto_o_indefinido($texto)
{
    return trim($texto ?? '') !== '' ? e($texto) : '<span class="indefinido">Indefinido</span>';
}

//----función que convierte una cadena separada por comas en un arreglo.
function lista_desde_comas($texto)
{
    //explode corta el texto en cada coma
    //array_map(trim) quita espacios
    //array_filter quita los vacíos 
    return array_values(array_filter(array_map('trim', explode(',', $texto ?? ''))));
}

/*
 * formatear_incisos(): hace legible el texto de una cláusula
 * ----------------------------------------------------------
 */
function formatear_incisos($texto)
{
    $texto = trim($texto ?? '');
    //----corta el texto justo antes de cada " a) ", " b) "... (una letra + paréntesis)
    $partes = preg_split('/\s+(?=[a-zñ]\)\s)/u', $texto);
    //----si el primer pedazo no empieza con "a)", es la introducción
    $intro = preg_match('/^[a-zñ]\)\s/u', $partes[0]) ? '' : array_shift($partes);

    $html = $intro !== '' ? '<p>' . nl2br(e($intro)) . '</p>' : '';

    if ($partes) {
        $html .= '<ul class="incisos">';
        foreach ($partes as $inciso) {
            $letra = mb_substr($inciso, 0, 2);         
            $resto = trim(mb_substr($inciso, 2));       
            $html .= '<li><strong>' . e($letra) . '</strong><span>' . nl2br(e($resto)) . '</span></li>';
        }
        $html .= '</ul>';
    }

    return $html;
}

/*
 * version(): evitar la caché del navegador
 * ----------------------------------------
 */
function version($archivo)
{
    $ruta = __DIR__ . '/../' . $archivo;
    // filemtime = fecha de última modificación del archivo (en segundos)
    return $archivo . '?v=' . (file_exists($ruta) ? filemtime($ruta) : '0');
}

/*
 * estilos(): carga varios archivos de la carpeta css/ de una vez
 * ---------------------------------------------------------------
 */
function estilos($raiz, ...$archivos)
{
    $html = '';
    foreach ($archivos as $archivo) {
        $html .= '<link href="' . $raiz . version("css/$archivo.css") . '" rel="stylesheet">' . "\n";
    }
    return $html;
}

/*
 * scripts_tablas(): los scripts que necesita toda página con tablas
 * -----------------------------------------------------------------
 * jQuery y DataTables están guardados en js/vendor/ (no se piden a internet):
 * cargan al instante y funcionan aunque falle la conexión.
 * Al final va el script propio de la página (ej. 'convenios.js').
 */
function scripts_tablas($raiz, $scriptPagina)
{
    $archivos = ['vendor/jquery-3.7.1.min.js', 'vendor/jquery.dataTables-1.13.6.min.js', 'tabla.js', $scriptPagina];

    $html = '';
    foreach ($archivos as $archivo) {
        $html .= '<script src="' . $raiz . version("js/$archivo") . '"></script>' . "\n";
    }
    return $html;
}
