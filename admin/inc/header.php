<?php
/*
 * ============================================================================
 * --------------------------------header--------------------------------------
 * ============================================================================
 * En lugar de copiar el <head> y el menú en cada página
 */
$seccion ??= '';

$menu = [
    'arrendatarios' => ['Arrendatarios', 'arrendatarios/index.php', 'tienda'],
    'parques'       => ['Parques', 'parques/index.php', 'parque'],
    'convenios'     => ['Convenios', 'convenios/index.php', 'documento'],
    'exonerados'    => ['Exonerados Apulo', 'exonerados/index.php', 'persona-check'],
];

//----recuerda la última sección visitada (cookie de 1 año): al volver a entrar a /admin, index.php lleva directo a ella.
//----Cookie y no $_SESSION porque la sesión se borra al cerrar sesión. Va antes de cualquier HTML: setcookie() envía un encabezado.
if (isset($menu[$seccion])) {
    setcookie('ultima_seccion', $seccion, ['expires' => time() + 31536000, 'path' => ADMIN_URL, 'httponly' => true, 'samesite' => 'Lax']);
}

$nombreUsuario = $_SESSION['usuario_nombre'] ?? '';

$titulo = ($titulo ?? 'Panel') . ' - Panel ISTU';
$raiz   = SITE_URL . '/';
$hojas  = ['base', 'paginas', 'tablas', 'modales', 'formularios', 'admin', 'preloader'];
require __DIR__ . '/../../inc/head.php';
?>

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