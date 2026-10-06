<?php
/*
 * ============================================================================
 * ARRENDATARIOS - SUBIR / REEMPLAZAR EL PDF DE POLÍTICA INTERNA
 * ============================================================================
 */
require_once __DIR__ . '/../inc/auth.php';

$errores = [];
$maxBytes = 10 * 1024 * 1024;   // 10 MB

//----Si el archivo supera post_max_size (php.ini), PHP descarta TODO el envío: $_POST y $_FILES llegan vacíos.
//----Sin esta revisión, csrf_verificar() diría "Solicitud inválida" en vez de explicar que el archivo es muy grande.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $errores[] = 'El archivo es demasiado grande para el servidor (límite de php.ini: ' . ini_get('post_max_size') . ').';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    $archivo = $_FILES['pdf'] ?? null;

    if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        $errores[] = 'Seleccione un archivo PDF.';
    } elseif ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        //----------UPLOAD_ERR_INI_SIZE = el archivo supera el límite de php.ini (upload_max_filesize)
        $errores[] = 'El archivo es demasiado grande para el servidor (límite de php.ini: ' . ini_get('upload_max_filesize') . ').';
    } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'No se pudo subir el archivo. Intente de nuevo.';
    } elseif ($archivo['size'] > $maxBytes) {
        $errores[] = 'El PDF no puede pesar más de 10 MB.';
    } else {
        /*
         * Analiza que el archivo sea pdf
         */
        $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if ($tipo !== 'application/pdf') {
            $errores[] = 'El archivo debe ser un PDF.';
        }
    }

    if (!$errores) {
        $carpeta = dirname(POLITICA_PDF_RUTA);

        //----se revisa paso a paso para decir EXACTAMENTE qué falló:
        if (!is_dir($carpeta) && !@mkdir($carpeta, 0755, true)) {
            $errores[] = 'No se pudo crear la carpeta uploads/. Créela a mano en la raíz del proyecto.';
        } elseif (!is_writable($carpeta)) {
            $errores[] = 'PHP no tiene permiso para escribir en la carpeta uploads/. Dele permiso de escritura.';
        } elseif (@move_uploaded_file($archivo['tmp_name'], POLITICA_PDF_RUTA)) {
            flash('La política interna se actualizó correctamente.');
            redirigir('arrendatarios/index.php');
        } else {
            //----error_get_last() = el último aviso de PHP (el motivo real del fallo)
            error_log('Política interna: no se pudo mover el PDF a ' . POLITICA_PDF_RUTA . ' - ' . (error_get_last()['message'] ?? 'sin detalle'));
            $errores[] = 'No se pudo guardar el archivo en el servidor. El detalle quedó en el registro de errores de PHP.';
        }
    }
}

$titulo       = 'Política interna';
$seccion      = 'arrendatarios';
$tituloForm   = file_exists(POLITICA_PDF_RUTA) ? 'Reemplazar política interna' : 'Subir política interna';
$subeArchivos = true;         
$textoBoton   = 'Subir PDF';   

require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>

<?php if (file_exists(POLITICA_PDF_RUTA)): ?>
    <p class="ayuda ayuda-grande">
        Ya hay un PDF publicado: <a href="<?= POLITICA_PDF_URL ?>" target="_blank" rel="noopener">ver el actual</a>.
        El que suba ahora lo reemplazará.
    </p>
<?php endif; ?>

<div class="grupo">
    <span class="grupo-titulo">Archivo PDF</span>
    <input id="pdf" name="pdf" type="file" accept="application/pdf" required class="solo-lectores archivo-input">
    <label for="pdf" class="selector-archivo">
        <span class="selector-icono"><?= icono('pdf', 22) ?></span>
        <span class="selector-texto">
            <strong id="nombreArchivo">Seleccione un archivo PDF</strong>
            <small>Haga clic aquí o arrastre el archivo · Máximo 10 MB</small>
        </span>
        <span class="btn btn-chico btn-suave">Examinar</span>
    </label>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
