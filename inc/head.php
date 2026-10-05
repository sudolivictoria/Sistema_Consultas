<?php
/*
 * ============================================================================
 * <HEAD> COMÚN de todas las páginas (inicio, consultas, login y panel)
 * ============================================================================
 *   Antes de incluirlo, cada página define:
 *   $titulo -> texto de la pestaña del navegador
 *   $raiz   -> cómo llegar a la raíz del sitio desde esa página ('', '../' o SITE_URL . '/')
 *   $hojas  -> archivos de css/ que necesita, sin la extensión .css
 */

//----@var le dice al editor (VS Code) que estas variables vienen de la página que incluye
/** @var string $titulo */
/** @var string $raiz */
/** @var string[] $hojas */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= e($titulo) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,500&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <?= estilos($raiz, ...$hojas) ?>
    <link rel="icon" type="image/png" href="<?= $raiz ?>images/logo.png" />
    <script src="<?= $raiz . version('js/preloader.js') ?>"></script>
</head>
