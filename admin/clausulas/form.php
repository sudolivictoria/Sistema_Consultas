<?php
/*
-----------------------------------------------------
 * CLÁUSULAS CONTRACTUALES - FORMULARIO (crear y editar)
-------------------------------------------------------
 */
require_once __DIR__ . '/../inc/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$errores = [];

//-----valores iniciales
$siguiente = $conexion->query("SELECT COALESCE(MAX(numero), 0) + 1 FROM clausulas")->fetch_row()[0];
$cla = ['numero' => $siguiente, 'titulo' => '', 'texto' => ''];

if ($id) {
    $stmt = $conexion->prepare("SELECT numero, titulo, texto FROM clausulas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    if (!$fila) {
        flash('La cláusula no existe.', 'error');
        redirigir('clausulas/index.php');
    }
    $cla = $fila;
}

//------procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();

    $cla = [
        'numero' => trim($_POST['numero'] ?? ''),
        'titulo' => trim($_POST['titulo'] ?? ''),
        'texto'  => trim($_POST['texto'] ?? ''),
    ];

    //----ctype_digit: true si el texto son solo dígitos (0-9)
    if (!ctype_digit($cla['numero']) || $cla['numero'] < 1) $errores[] = 'El número debe ser un entero mayor que 0.';
    if ($cla['titulo'] === '') $errores[] = 'El título es obligatorio.';
    if ($cla['texto'] === '') $errores[] = 'El texto de la cláusula es obligatorio.';

    if (!$errores) {
        $valores = [$cla['numero'], $cla['titulo'], $cla['texto']];
        $campos  = "numero = ?, titulo = ?, texto = ?";

        if ($id) {
            $valores[] = $id;
            $conexion->prepare("UPDATE clausulas SET $campos WHERE id = ?")->execute($valores);
        } else {
            $conexion->prepare("INSERT INTO clausulas SET $campos")->execute($valores);
        }

        flash($id ? 'La cláusula se actualizó correctamente.' : 'La cláusula se creó correctamente.');
        redirigir('clausulas/index.php');
    }
}

//-------listado de fondo mas formulario lateral
$titulo     = $id ? 'Editar cláusula' : 'Nueva cláusula';
$seccion    = 'arrendatarios';
$tituloForm = $titulo;

require __DIR__ . '/../inc/header.php';
require __DIR__ . '/listado.php';
require __DIR__ . '/../inc/drawer_inicio.php';
?>

<div class="grupo">
    <label for="numero">Número</label>
    <input id="numero" name="numero" type="number" min="1" required class="entrada" value="<?= e($cla['numero']) ?>">
    <span class="ayuda">Define el orden: 1 = cláusula primera.</span>
</div>

<div class="grupo">
    <label for="titulo">Título</label>
    <input id="titulo" name="titulo" type="text" maxlength="150" required class="entrada" value="<?= e($cla['titulo']) ?>">
</div>

<div class="grupo">
    <label for="texto">Texto completo</label>
    <textarea id="texto" name="texto" rows="10" required class="entrada"><?= e($cla['texto']) ?></textarea>
</div>

<?php
require __DIR__ . '/../inc/drawer_fin.php';
require __DIR__ . '/../inc/footer.php';
