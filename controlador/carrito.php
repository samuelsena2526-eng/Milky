<?php
require_once __DIR__ . '/auth.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../carrito.php');
    exit;
}

if (!isset($_SESSION['token_carrito']) || !hash_equals($_SESSION['token_carrito'], (string)($_POST['token'] ?? ''))) {
    header('Location: ../carrito.php?estado=sesion');
    exit;
}

$usuarioId = obtenerUsuarioSesionId();
if ($usuarioId === null) {
    header('Location: ../carrito.php?estado=usuario');
    exit;
}

$consultaCarrito = $pdo->prepare('SELECT id FROM carritos WHERE usuario_id = ? LIMIT 1');
$consultaCarrito->execute([$usuarioId]);
$carritoId = $consultaCarrito->fetchColumn();
$accion = (string)($_POST['accion'] ?? '');

if ($accion === 'agregar') {
    $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    if (!$productoId || !$cantidad || $cantidad < 1 || $cantidad > 99) {
        header('Location: ../productosSU.php?estado=datos');
        exit;
    }
    $productoExiste = $pdo->prepare('SELECT id FROM productos WHERE id = ?');
    $productoExiste->execute([$productoId]);
    if (!$productoExiste->fetchColumn()) {
        header('Location: ../productosSU.php?estado=no_encontrado');
        exit;
    }
    if (!$carritoId) {
        $crearCarrito = $pdo->prepare('INSERT INTO carritos (usuario_id) VALUES (?)');
        $crearCarrito->execute([(int)$usuarioId]);
        $carritoId = (int)$pdo->lastInsertId();
    }
    $agregar = $pdo->prepare(
        'INSERT INTO carrito_items (carrito_id, producto_id, cantidad) VALUES (?, ?, ?) '
        . 'ON DUPLICATE KEY UPDATE cantidad = LEAST(cantidad + VALUES(cantidad), 99)'
    );
    $agregar->execute([(int)$carritoId, $productoId, $cantidad]);
    header('Location: ../carrito.php?estado=agregado');
    exit;
}

if (!$carritoId) {
    header('Location: ../carrito.php?estado=vacio');
    exit;
}

if ($accion === 'actualizar') {
    $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    if (!$itemId || !$cantidad || $cantidad < 1 || $cantidad > 99) {
        header('Location: ../carrito.php?estado=cantidad');
        exit;
    }
    $actualizar = $pdo->prepare('UPDATE carrito_items SET cantidad = ? WHERE id = ? AND carrito_id = ?');
    $actualizar->execute([$cantidad, $itemId, (int)$carritoId]);
    header('Location: ../carrito.php?estado=actualizado');
    exit;
}

if ($accion === 'eliminar') {
    $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    if ($itemId) {
        $eliminar = $pdo->prepare('DELETE FROM carrito_items WHERE id = ? AND carrito_id = ?');
        $eliminar->execute([$itemId, (int)$carritoId]);
    }
    header('Location: ../carrito.php?estado=eliminado');
    exit;
}

if ($accion === 'vaciar') {
    $vaciar = $pdo->prepare('DELETE FROM carrito_items WHERE carrito_id = ?');
    $vaciar->execute([(int)$carritoId]);
    header('Location: ../carrito.php?estado=vacio');
    exit;
}

header('Location: ../carrito.php');
exit;
