<?php
/*
 * ============================================================================
 * FUNCIONES COMPARTIDAS (sitio público + panel /admin)
 * ============================================================================
 * Las funciones que solo usa el admin (sesión, CSRF, mensajes) están en inc/admin_funciones.php
 * Las funciones que usa el sitio público (inicio, exonerados, convenios, arrendatarios) están en este archivo
 */

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
        : '<span class="estado estado-vencido">Vencido</span>';
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
