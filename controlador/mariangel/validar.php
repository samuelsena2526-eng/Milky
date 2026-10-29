<?php
session_start();
require_once __DIR__ . '/../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../InicioS.php');
    exit;
}

$correo = strtolower(trim((string)($_POST['correo'] ?? '')));
$contrasena = $_POST['contraseña'] ?? '';
if ($correo === '' || !is_string($contrasena) || $contrasena === '') {
    header('Location: ../../InicioS.php?error=campos');
    exit;
}

$consulta = $conexion->prepare('SELECT id, nombre, correo, rol, `contraseña` FROM usuarios WHERE correo = ? LIMIT 1');
$consulta->execute([$correo]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);
$guardado = (string)($usuario['contraseña'] ?? '');

// Las cuentas antiguas guardaban texto plano. Verificarlas una vez y convertirlas
// al formato seguro para que no queden almacenadas así después de iniciar sesión.
$infoHash = $guardado !== '' ? password_get_info($guardado) : [];
$esHash = $guardado !== '' && !empty($infoHash['algo']);
$hashIncompleto = preg_match('/^\$(2[aby]|argon2i|argon2id)\$/', $guardado) === 1
    && strlen($guardado) < 60;
$valida = $usuario && $guardado !== '' && ($esHash
    ? password_verify($contrasena, $guardado)
    : hash_equals($guardado, $contrasena));

if (!$valida) {
    header('Location: ../../InicioS.php?error=' . ($hashIncompleto ? 'hash_incompleto' : 'credenciales'));
    exit;
}

if (!$esHash || password_needs_rehash($guardado, PASSWORD_DEFAULT)) {
    $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
    $actualizar = $conexion->prepare('UPDATE usuarios SET `contraseña` = ? WHERE id = ?');
    $actualizar->execute([$nuevoHash, (int)$usuario['id']]);
}

session_regenerate_id(true);
// Mantener el formato de sesión que ya consumen los controladores de esta sección.
$_SESSION['usuario'] = $usuario['correo'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['rol'] = $usuario['rol'] ?: 'cliente';

header('Location: ../../index.php' . ($_SESSION['rol'] === 'Admin' ? '?bienvenida=1' : ''));
exit;
