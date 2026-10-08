<?php
/*
 *----------------------------------------------------------------------------
 *                    CONSULTA PÚBLICA DE ARRENDATARIOS
 * ---------------------------------------------------------------------------
 * Cada parque solo ve SUS arrendatarios: primero se escribe la clave del parque
 * (botón "Ver mi parque") y luego se muestra la tabla con buscador + normativa
 * (PDF de normativa interna y ventana de cláusulas, iguales para todos los parques).
 * La ventana de cláusulas está aparte, en inc/modal_clausulas.php.
 * La lógica de la clave está en inc/acceso_parque.php; las ventanas y el buscador, en js/arrendatarios.js.
 */
$titulo     = 'Consulta de arrendatarios';
$hojasExtra = ['formularios'];   //----estilos del campo de la clave

//----Esta página usa sesión (para recordar el parque): la conexión, las funciones y la sesión
//----van ANTES de cualquier HTML. publico_inicio.php usa require_once: no los vuelve a cargar.
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../inc/funciones.php';
require_once __DIR__ . '/../inc/acceso_parque.php';
iniciar_sesion();

//------PROCESAR LA CLAVE (entrar) o el botón "Cambiar de parque" (salir)
$errorClave = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    if (($_POST['accion'] ?? '') === 'salir') {
        salir_parque();
    } else {
        //---entrar_parque() devuelve null si la clave es correcta, o el mensaje de error
        $errorClave = entrar_parque($conexion, $_POST['clave'] ?? '');
    }
    //---si todo salió bien: POST -> redirigir -> GET (con F5 no se vuelve a enviar la clave)
    if (!$errorClave) {
        header('Location: arrendatarios.php');
        exit;
    }
    //---si la clave fue incorrecta NO redirigimos: la ventana se vuelve a abrir con el error
}

//---null si todavía no ha escrito la clave de ningún parque
$parque = parque_actual($conexion);

require __DIR__ . '/../inc/publico_inicio.php';

//---solo se consultan los datos cuando ya entró a un parque, y solo los de ESE parque
if ($parque) {
    $stmt = $conexion->prepare(
        "SELECT nombre_arrendatario, local_comercial, venta_autorizada FROM arrendatarios
         WHERE parque_id = ? ORDER BY nombre_arrendatario"
    );
    $stmt->execute([$parque['id']]);
    $arrendatarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

//---revisa si el archivo de normativa interna existe (se usa aquí y en la ventana de cláusulas)
$hayPdf = file_exists(NORMATIVA_PDF_RUTA);
?>

<div class="pagina-titulos">
    <nav class="ruta" aria-label="Ruta">Consultas / <span>Arrendatarios</span></nav>
    <div class="titulo-linea">
        <h1 class="titulo">Consulta de <span>arrendatarios</span></h1>
        <span class="insignia"><?= date('Y') ?></span>
    </div>
    <p class="subtitulo">Verifique qué servicios tiene autorizados cada local comercial durante la supervisión de contratos.</p>
</div>

<?php if (!$parque): ?>
    <!-------------------SIN PARQUE: se pide la clave antes de mostrar cualquier dato------------------->
    <section class="panel acceso-parque">
        <span class="acceso-icono"><?= icono('candado', 26) ?></span>
        <h2>Ingrese a su parque</h2>
        <p>Cada parque tiene su propia clave. Con ella verá solo los arrendatarios de su parque.</p>
        <button type="button" id="btnVerParque" class="btn btn-primario">
            <?= icono('parque', 18) ?> Ver mi parque
        </button>
    </section>

    <!---------------VENTANA DE LA CLAVE (tipo login)-------------->
    <!--data-abrir: si la clave fue incorrecta, js/arrendatarios.js la abre sola para mostrar el error-->
    <dialog id="modalParque" class="modal modal-clave" aria-labelledby="titulo-parque" <?= $errorClave ? 'data-abrir' : '' ?>>
        <!--Sin action="": la clave se envía a esta misma página (por POST)-->
        <form method="post">
            <?= csrf_campo() ?>
            <div class="modal-cabecera">
                <div class="icono-caja"><?= icono('candado', 22) ?></div>
                <div class="modal-titulos">
                    <h2 id="titulo-parque">Ver mi parque</h2>
                    <span>Escriba la clave que le proporcionaron para su parque.</span>
                </div>
                <button type="button" class="btn-cuadrado btn-cerrar-modal" aria-label="Cerrar"><?= icono('cerrar', 18, 2.2) ?></button>
            </div>

            <div class="modal-cuerpo">
                <?php if ($errorClave): ?>
                    <div class="errores" role="alert"><?= e($errorClave) ?></div>
                <?php endif; ?>
                <div class="grupo">
                    <label for="clave">Clave del parque</label>
                    <div class="campo-icono">
                        <?= icono('candado') ?>
                        <!--autofocus: al abrir la ventana, el cursor queda listo para escribir la clave-->
                        <input id="clave" name="clave" type="password" required autofocus autocomplete="current-password" placeholder="••••••••">
                        <!--Botón del ojo: cambia el campo entre "password" (oculto) y "text" (visible)-->
                        <button type="button" class="ver-clave" id="verClave" aria-label="Mostrar clave"><?= icono('ojo') ?></button>
                    </div>
                </div>
            </div>

            <div class="modal-pie">
                <button type="button" class="btn btn-fantasma btn-cerrar-modal">Cancelar</button>
                <button type="submit" class="btn btn-primario">Entrar <?= icono('flecha-der', 17, 2.2) ?></button>
            </div>
        </form>
    </dialog>

<?php else: ?>
<section class="panel">
    <div class="panel-busqueda">
        <!-------------------PARQUE ACTUAL + botón para salir y escribir otra clave------------------->
        <div class="parque-actual">
            <span class="parque-nombre">
                <span class="parque-icono"><?= icono('parque', 18) ?></span>
                <span>
                    <small>Su parque</small>
                    <strong><?= e($parque['nombre']) ?></strong>
                </span>
            </span>
            <form method="post">
                <?= csrf_campo() ?>
                <input type="hidden" name="accion" value="salir">
                <button type="submit" class="btn btn-chico btn-fantasma"><?= icono('salir', 16) ?> Salir</button>
            </form>
        </div>

        <label for="buscadorCustom">Buscar arrendatario, local o servicio</label>
        <div class="busqueda-fila">
            <div class="campo-busqueda">
                <?= icono('buscar', 20, 2.2) ?>
                <input id="buscadorCustom" type="search" placeholder="Ej.: nombre del local o un producto…" autocomplete="off">
            </div>
        </div>

        <!--------------------NORMATIVA INTERNA------------>
        <div class="normativa-fila">
            <span>Normativa:</span>
            <?php if ($hayPdf): ?>
                <a href="../<?= version('uploads/normativa-interna.pdf') ?>" target="_blank" rel="noopener" class="btn btn-contorno btn-normativa">
                    <?= icono('pdf', 17) ?> Normativa interna <span class="mini-insignia">PDF</span> <?= icono('externo', 14, 2.2) ?>
                </a>
            <?php else: ?>
                <!--sin pdf el boton de la normativa esta desactivado-->
                <button type="button" class="btn btn-contorno btn-normativa" disabled title="Aún no se ha publicado">
                    <?= icono('pdf', 17) ?> Normativa interna <span class="mini-insignia">PDF</span>
                </button>
            <?php endif; ?>
            <button type="button" id="btnClausulas" class="btn btn-primario btn-normativa">
                <?= icono('lista', 17) ?> Cláusulas contractuales
            </button>
        </div>
    </div>

    <!------TABLA DE ARRENDATARIOS: con buscador y filtros manejados por js/arrendatarios.js------>
    <table id="tablaArrendatarios" class="tabla">
        <thead>
            <tr>
                <!--anchos fijos (ver css/tablas.css): así las columnas no cambian de tamaño al pasar de página-->
                <th scope="col" class="col-arrendatario">Arrendatario</th>
                <th scope="col" class="col-local">Local comercial</th>
                <th scope="col">Venta autorizada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arrendatarios as $a): ?>
                <tr>
                    <?php $productos = lista_desde_comas($a['venta_autorizada']); ?>
                    <!--texto_o_indefinido(): si el dato viene vacío o NULL, muestra "Indefinido"-->
                    <td data-label="Arrendatario" class="fuerte arriba"><?= e($a['nombre_arrendatario']) ?></td>
                    <td data-label="Local" class="arriba">
                        <span class="con-icono"><?= icono('local', 16) ?><?= texto_o_indefinido($a['local_comercial']) ?></span>
                    </td>
                    <td data-label="Venta" class="arriba">
                        <!--ETIQUETA X PRODUCTO (o "Indefinido" si no hay ninguno)-->
                        <?php if ($productos): ?>
                            <div class="chips">
                                <?php foreach ($productos as $producto): ?>
                                    <span class="chip"><?= e($producto) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?= texto_o_indefinido(null) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!---------------VENTANA DE CLÁUSULAS: está en inc/modal_clausulas.php-------------->
<?php require __DIR__ . '/../inc/modal_clausulas.php'; ?>
<?php endif; ?>

<?php
$script = 'arrendatarios.js';
require __DIR__ . '/../inc/publico_fin.php';
