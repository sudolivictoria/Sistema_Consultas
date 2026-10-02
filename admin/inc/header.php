<?php
/*
 * ============================================================================
 * --------------------------------header--------------------------------------
 * ============================================================================
 * En lugar de copiar el <head> y el menú en cada página
 */
$titulo  ??= 'Panel';
$seccion ??= '';

$menu = [
    'convenios'     => ['Convenios', 'convenios/index.php', 'documento'],
    'exonerados'    => ['Exonerados Apulo', 'exonerados/index.php', 'persona-check'],
    'arrendatarios' => ['Arrendatarios', 'arrendatarios/index.php', 'tienda'],
];

$nombreUsuario = $_SESSION['usuario_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= e($titulo) ?> - Panel ISTU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <!--Estilos que usa el panel (cada archivo de css/ es un tema)-->
    <?= estilos(SITE_URL . '/', 'base', 'paginas', 'tablas', 'modales', 'formularios', 'admin', 'preloader') ?>
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/images/logo.png" />
    <script src="<?= SITE_URL ?>/<?= version('js/preloader.js') ?>"></script>
</head>

<body class="fondo-cuadricula">
    <?php require __DIR__ . '/../../inc/preloader.php'; ?>

    <div class="admin">

        <!--===================== MENÚ LATERAL===================== -->
        <aside class="admin-lateral">
            <div class="admin-marca">
                <div class="marca-logo">ISTU</div>
                <div class="marca-texto">
                    <strong>Consultas ISTU</strong>
                    <span>Administración</span>
                </div>
            </div>

            <nav class="admin-nav" aria-label="Administración">
                <span class="admin-nav-titulo">Registros</span>
                <?php
                //----foreach recorre el arreglo $menu. Con [$texto, $ruta, $icono]
                //----separamos de una vez los 3 valores de cada opción.
                foreach ($menu as $clave => [$texto, $ruta, $icono]):
                    $activa = $seccion === $clave;
                ?>
                    <!--aria-current="page" le dice a los lectores de pantalla cuál es la página actual-->
                    <a href="<?= ADMIN_URL . '/' . $ruta ?>" class="nav-item<?= $activa ? ' activo' : '' ?>" <?= $activa ? 'aria-current="page"' : '' ?>>
                        <?= icono($icono) ?><?= $texto ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="admin-lateral-pie">
                <a href="<?= SITE_URL ?>/index.php" class="nav-item" target="_blank">
                    <?= icono('portal') ?>Ver portal público
                </a>
                <div class="usuario-tarjeta">
                    <!--Inicial y nombre guardados en la sesión al iniciar sesión-->
                    <div class="usuario-avatar"><?= e(inicial($nombreUsuario)) ?></div>
                    <div class="usuario-datos">
                        <strong><?= e($nombreUsuario) ?></strong>
                        <span>Administrador</span>
                    </div>
                    <a href="<?= ADMIN_URL ?>/logout.php" class="btn-salir" aria-label="Cerrar sesión" title="Cerrar sesión">
                        <?= icono('salir') ?>
                    </a>
                </div>
            </div>
        </aside>

        <!-- =====================CONTENIDO=====================-->
        <div class="admin-cuerpo">
            <div class="barra-acento"></div>
            <main class="admin-main">
