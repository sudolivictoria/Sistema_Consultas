<?php
/*
 * ============================================================================
 * ******************************ÍCONOS SVG************************************
 * ============================================================================
 * <?= icono('mas', 18, 2.4) ?>    -> nombre, tamaño, grosor
 * Los íconos toman el color del texto que los rodea (stroke="currentColor"),
 */

function icono($nombre, $tam = 18, $grosor = 2)
{
    static $trazos = [
        'buscar'        => '<circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path>',
        'flecha-izq'    => '<path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path>',
        'flecha-der'    => '<path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path>',
        'chevron-abajo' => '<path d="M6 9l6 6 6-6"></path>',
        'ojo'           => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle>',
        'documento'     => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>',
        'pdf'           => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path>',
        'lista'         => '<path d="M9 6h11"></path><path d="M9 12h11"></path><path d="M9 18h11"></path><path d="M4 6h.01"></path><path d="M4 12h.01"></path><path d="M4 18h.01"></path>',
        'externo'       => '<path d="M7 17L17 7"></path><path d="M8 7h9v9"></path>',
        'cerrar'        => '<path d="M18 6L6 18"></path><path d="M6 6l12 12"></path>',
        'mas'           => '<path d="M12 5v14"></path><path d="M5 12h14"></path>',
        'editar'        => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path>',
        'eliminar'      => '<path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path>',
        'salir'         => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path>',
        'portal'        => '<path d="M14 3h7v7"></path><path d="M10 14L21 3"></path><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"></path>',
        'persona-check' => '<circle cx="9" cy="8" r="4"></circle><path d="M2 21c0-3.9 3.1-7 7-7 1.6 0 3.1.5 4.3 1.4"></path><path d="M15.5 18.5l2 2 4-4.5"></path>',
        'tienda'        => '<path d="M3 9l1.5-5h15L21 9"></path><path d="M3 9h18v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0z"></path><path d="M5 13v8h14v-8"></path><path d="M10 21v-5h4v5"></path>',
        'parque'        => '<path d="M8 19a4 4 0 0 1-2.24-7.32A3.5 3.5 0 0 1 9 6.03V6a3 3 0 1 1 6 0v.04a3.5 3.5 0 0 1 3.24 5.65A4 4 0 0 1 16 19z"></path><path d="M12 19v3"></path>',
        'local'         =>'<path d="M3 9l1.5-5h15L21 9"></path><path d="M5 9v12h14V9"></path><path d="M10 21v-5h4v5"></path>',
        'candado'       => '<rect x="4" y="11" width="16" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path>',
        'usuario'       => '<circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"></path>',
        'ticket'        => '<path d="M3 9a2 2 0 0 0 0 6v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3a2 2 0 0 0 0-6V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"></path><path d="M9.5 14.5l5-5"></path>',
        'etiqueta'      => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"></path><path d="M7.5 7.5h.01"></path>',
        'mensaje'       => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
        'check'         => '<path d="M5 12l5 5 9-10"></path>',
        'subir'         => '<path d="M12 16V4"></path><path d="M6 10l6-6 6 6"></path><path d="M4 20h16"></path>',
        'alerta'        => '<circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path>',
    ];

    return '<svg width="' . $tam . '" height="' . $tam . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="' . $grosor . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($trazos[$nombre] ?? '') . '</svg>';
}
