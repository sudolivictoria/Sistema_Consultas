<?php
/*
 * PANEL LATERAL (drawer) - parte de abajo: botones Cancelar y Guardar.
 * Si la página define $textoBoton, se usa ese texto en lugar de "Guardar".
 */
?>
            </div>
            <div class="drawer-pie">
                <a href="index.php" class="btn btn-fantasma">Cancelar</a>
                <button type="submit" class="btn btn-primario"><?= e($textoBoton ?? 'Guardar') ?></button>
            </div>
        </form>
    </div>
</div>
