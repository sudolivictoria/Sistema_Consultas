<?php
/*
 *----------------------------------------------------------------------------
 *         PARTE DE ABAJO DE LAS CONSULTAS PÚBLICAS: cierra lo que abrió
 * publico_inicio.php y carga los scripts. Cada página define antes:
 * $script -> su archivo de js
 * ----------------------------------------------------------------------------
 */
?>
    </main>

    <!--jQuery + DataTables (búsqueda y páginas) + configuración común + script de la página-->
    <?= scripts_tablas('../', $script) ?>
</body>

</html>
