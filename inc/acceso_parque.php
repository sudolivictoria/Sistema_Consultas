<?php
/*
 * ============================================================================
 *          ACCESO POR PARQUE (consulta pública de arrendatarios)
 * ============================================================================
 * Cada parque tiene su propia clave, guardada como HASH en parques.clave
 */

//----Para que nadie pruebe claves sin parar: después de INTENTOS_MAXIMOS claves incorrectas
//----desde la misma dirección IP, hay que esperar MINUTOS_BLOQUEO minutos.
const INTENTOS_MAXIMOS = 10;
const MINUTOS_BLOQUEO  = 15;

//----parque_por_clave(): devuelve el parque cuya clave coincide, o null.
//----$excepto: id de un parque que no se revisa (el admin lo usa para no repetir claves entre parques).
function parque_por_clave($conexion, $clave, $excepto = 0)
{
    if ($clave === '') return null;

    //----son pocos parques: se revisa la clave contra el hash de cada uno con password_verify()
    $parques = $conexion->query("SELECT id, nombre, clave FROM parques WHERE clave IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
    foreach ($parques as $p) {
        if ((int) $p['id'] !== (int) $excepto && password_verify($clave, $p['clave'])) {
            return $p;
        }
    }
    return null;
}

//----entrar_parque(): revisa la clave escrita. Devuelve null si entró, o el mensaje de error.
function entrar_parque($conexion, $clave)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    //----los intentos más viejos que MINUTOS_BLOQUEO ya no cuentan: se borran
    $conexion->query("DELETE FROM intentos_parque WHERE fecha < NOW() - INTERVAL " . MINUTOS_BLOQUEO . " MINUTE");

    $stmt = $conexion->prepare("SELECT COUNT(*) FROM intentos_parque WHERE ip = ?");
    $stmt->execute([$ip]);
    if ($stmt->get_result()->fetch_row()[0] >= INTENTOS_MAXIMOS) {
        return 'Demasiados intentos con una clave incorrecta. Espere ' . MINUTOS_BLOQUEO . ' minutos e intente de nuevo.';
    }

    $parque = parque_por_clave($conexion, trim($clave));
    if (!$parque) {
        //----se anota el intento fallido (la fecha la pone MySQL con NOW(), igual que en el DELETE de arriba)
        $conexion->prepare("INSERT INTO intentos_parque (ip, fecha) VALUES (?, NOW())")->execute([$ip]);
        return 'La clave no es correcta. Revísela e intente de nuevo.';
    }

    //----clave correcta: se borran sus intentos fallidos y se guarda el parque en la sesión.
    //----session_regenerate_id(): nuevo id de sesión al "iniciar sesión" (igual que en el login del admin).
    $conexion->prepare("DELETE FROM intentos_parque WHERE ip = ?")->execute([$ip]);
    session_regenerate_id(true);
    //----"huella" = el hash de la clave con que entró. Si el admin cambia la clave, la huella
    //----ya no coincide y parque_actual() saca a quien estaba dentro.
    //----"hasta": el acceso vence a las 8 horas de haber entrado (DURACION_SESION, en inc/funciones.php)
    $_SESSION['parque'] = ['id' => (int) $parque['id'], 'huella' => $parque['clave'], 'hasta' => time() + DURACION_SESION];
    return null;
}

//----parque_actual(): el parque en el que está la persona (id, nombre), o null si no ha entrado.
function parque_actual($conexion)
{
    $acceso = $_SESSION['parque'] ?? null;
    if (!$acceso) return null;

    //----pasaron las 8 horas: hay que volver a escribir la clave
    if (time() > ($acceso['hasta'] ?? 0)) {
        salir_parque();
        return null;
    }

    $stmt = $conexion->prepare("SELECT id, nombre, clave FROM parques WHERE id = ?");
    $stmt->execute([$acceso['id']]);
    $parque = $stmt->get_result()->fetch_assoc();

    //----si el parque ya no existe o su clave cambió, se pierde el acceso
    if (!$parque || !hash_equals((string) $parque['clave'], $acceso['huella'])) {
        salir_parque();
        return null;
    }
    return $parque;
}

//----salir_parque(): botón "Cambiar de parque"
function salir_parque()
{
    unset($_SESSION['parque']);
}
