<?php
/*
 * PARTE DE ABAJO DE LAS CONSULTAS PÚBLICAS: cierra lo que abrió
 * publico_inicio.php y carga los scripts. Cada página define antes:
 */

$script ??= '';  
?>
    </main>

    <!--jQuery + DataTables (búsqueda y páginas) + configuración común + script de la página-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../<?= version('js/tabla.js') ?>"></script>
    <script src="../<?= version('js/' . $script) ?>"></script>
</body>

</html>
