<?php
/*
 *----------------------------------------------------------------------------
 * FOOTER: cierra lo que abrió header.php y agrega 
 * La notificación (toast) con el mensaje flash, si hay uno
 * La ventana "¿Eliminar este registro?" (la abre js/admin.js)
 * ----------------------------------------------------------------------------
 */

//--------------------------NOTIFICACIONES--------------------------------------

//-------------Título de la notificación según el tipo de mensaje flash-------------
$titulosToast = ['ok' => 'Cambios guardados', 'eliminado' => 'Registro eliminado', 'error' => 'Atención'];
?>
            </main>
        </div>
    </div>

    <?php if ($f = flash()): ?>
        <!--role="status": los lectores de pantalla leen el mensaje cuando aparece---->
        <div id="toast" class="toast toast-<?= e($f[1]) ?>" role="status" aria-live="polite">
            <span class="toast-icono"><?= icono($f[1] === 'ok' ? 'check' : ($f[1] === 'error' ? 'alerta' : 'eliminar'), 18, 2.6) ?></span>
            <div class="toast-texto">
                <strong><?= $titulosToast[$f[1]] ?? 'Aviso' ?></strong>
                <span><?= e($f[0]) ?></span>
            </div>
            <button type="button" class="toast-cerrar" aria-label="Cerrar notificación"><?= icono('cerrar', 16, 2.2) ?></button>
        </div>
    <?php endif; ?>

    <!-----confirmación de eliminar: una sola para todo el admin, admin.js le pone el texto--->
    <dialog id="dialogoEliminar" class="dialogo-confirmar" aria-labelledby="tituloEliminar">
        <div class="confirmar-icono"><?= icono('eliminar', 22) ?></div>
        <div>
            <h2 id="tituloEliminar">¿Eliminar este registro?</h2>
            <p>Se eliminará <strong id="nombreEliminar"></strong>. Esta acción no se puede deshacer.</p>
        </div>
        <div class="confirmar-botones">
            <button type="button" class="btn btn-fantasma" id="cancelarEliminar">Cancelar</button>
            <button type="button" class="btn btn-peligro" id="confirmarEliminar">Sí, eliminar</button>
        </div>
    </dialog>

    <!--jQuery, DataTables, configuración común de tablas y el JS del admin-->
    <?= scripts_tablas(SITE_URL . '/', 'admin.js') ?>
</body>

</html>
