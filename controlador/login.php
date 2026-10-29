<?php
require_once __DIR__ . "/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$contraseña = $_POST['contraseña'] ?? '';
$productoId = (int)($_POST['producto_id'] ?? 0);

if ($nombre === '' || $contraseña === '') {
    $destino = $productoId > 0
        ? "../registro.php?id=$productoId&compra=1&error=campos"
        : "../login.php?error=campos";

    header("Location: $destino");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = ? LIMIT 1");
$stmt->execute([$nombre]);
$registro = $stmt->fetch();
$hashContraseña = $registro['contraseña'] ?? '';
$contraseñaOk = $registro && $hashContraseña !== ''
    && (password_verify($contraseña, $hashContraseña) || hash_equals($hashContraseña, $contraseña));

if (!$registro || !$contraseñaOk) {
    $destino = $productoId > 0
        ? "../registro.php?id=$productoId&compra=1&error=credenciales"
        : "../login.php?error=credenciales";

    header("Location: $destino");
    exit;
}

session_regenerate_id(true);

$_SESSION['usuario'] = [
    'id' => (int)($registro['id'] ?? 0),
    'usuario' => $registro['nombre'] ?? $nombre,
    'rol' => $registro['rol'] ?? 'cliente',
    'nombre' => $registro['nombre'] ?? $nombre,
];
$_SESSION['rol'] = $_SESSION['usuario']['rol'];
$_SESSION['nombre'] = $_SESSION['usuario']['nombre'];

if ($productoId > 0) {
    $_SESSION['producto_pendiente'] = [
        'producto_id' => $productoId,
        'cantidad' => 1,
    ];

    header("Location: ../productos.php?compra=lista");
    exit;
}

header("Location: ../productos.php");
exit;
