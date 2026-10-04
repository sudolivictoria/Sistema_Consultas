<?php
/*
 *-------------------------------------------
 * Lista de errores de validación de un formulario.
 * ------------------------------------------
 */
if (!empty($errores)): ?>
    <div class="errores" role="alert">
        <strong>Revise lo siguiente:</strong>
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
