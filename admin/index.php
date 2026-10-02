<?php
/*
 * ============================================================================
 * *********************************LOGIN**************************************
 * ============================================================================
 * - GET  (se abre la URL) -> solo muestra el formulario
 * - POST (se envía el formulario) -> revisa usuario y contraseña
 * Ojo: aquí se usa bootstrap.php y NO auth.php
 */
require_once __DIR__ . '/inc/bootstrap.php';

if (!empty($_SESSION['usuario_id'])) {
    redirigir('convenios/index.php');
}
$error   = '';
$usuario = '';

//---------$_SERVER['REQUEST_METHOD'] dice si la página se pidió con GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    //-------$_POST tiene lo que se escribió en el formulario (por el atributo name="")
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    /*
     * CONSULTA PREPARADA (muy importante)
     * Nunca se mete lo que escribió el usuario directo en el SQL 
     */
    $stmt = $conexion->prepare("SELECT id, nombre, password FROM usuarios WHERE usuario = ?");
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();   // un arreglo con la fila, o null si no existe

    /*
     * password_verify compara la clave escrita con el HASH guardado.
     */
    if ($fila && password_verify($clave, $fila['password'])) {
        session_regenerate_id(true);

        //----Desde aquí, auth.php sabe que esta persona inició sesión
        $_SESSION['usuario_id']     = $fila['id'];
        $_SESSION['usuario_nombre'] = $fila['nombre'];
        redirigir('convenios/index.php');
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Iniciar sesión - Panel ISTU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <?= estilos(SITE_URL . '/', 'base', 'formularios', 'login', 'preloader') ?>
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/images/logo.png" />
    <script src="<?= SITE_URL ?>/<?= version('js/preloader.js') ?>"></script>
</head>

<body class="fondo-cuadricula login-pagina">
    <?php require __DIR__ . '/../inc/preloader.php'; ?>

    <div class="barra-acento-gruesa"></div>

    <main class="login-main">
        <div class="login-caja">
            <div class="login-tarjeta">
                <div class="login-cabecera">
                    <div class="login-logo">ISTU</div>
                    <h1>Plataforma de Consultas <span>ISTU</span></h1>
                    <span class="linea-acento"></span>
                    <p>Acceso para la administración de registros</p>
                </div>

                <!--Sin action="": el formulario se envía a esta misma página (por POST)-->
                <form method="post" class="login-form">
                    <?= csrf_campo() ?>

                    <?php if ($error): ?>
                        <div class="errores" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>

                    <div class="grupo">
                        <label for="usuario">Usuario</label>
                        <div class="campo-icono">
                            <?= icono('usuario') ?>
                            <input id="usuario" name="usuario" type="text" required autofocus autocomplete="username" placeholder="Nombre de usuario" value="<?= e($usuario) ?>">
                        </div>
                    </div>

                    <div class="grupo">
                        <label for="clave">Contraseña</label>
                        <div class="campo-icono">
                            <?= icono('candado') ?>
                            <input id="clave" name="clave" type="password" required autocomplete="current-password" placeholder="••••••••">
                            <!--Botón del ojo: cambia el campo entre "password" (oculto) y "text" (visible)-->
                            <button type="button" class="ver-clave" id="verClave" aria-label="Mostrar contraseña"><?= icono('ojo') ?></button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primario btn-login">
                        Iniciar sesión
                        <?= icono('flecha-der', 17, 2.2) ?>
                    </button>
                </form>
            </div>

            <a href="<?= SITE_URL ?>/index.php" class="login-volver">
                <?= icono('flecha-izq', 15, 2.2) ?>
                Volver a las consultas públicas
            </a>
            <p class="pie-texto">Unidad Jurídica · ISTU</p>
        </div>
    </main>

    <script>
        //----------Mostrar / ocultar la contraseña------------------
        const botonVer = document.getElementById('verClave');
        const campoClave = document.getElementById('clave');
        botonVer.addEventListener('click', function() {
            const oculta = campoClave.type === 'password';
            campoClave.type = oculta ? 'text' : 'password';
            botonVer.setAttribute('aria-label', oculta ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    </script>
</body>

</html>