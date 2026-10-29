<?php
require_once __DIR__ . '/controlador/auth.php';
requerirLogin();

$usuarioId = obtenerUsuarioSesionId();
if ($usuarioId === null) {
    header('Location: login.php');
    exit;
}

$consulta = $pdo->prepare(
    'SELECT ci.id AS item_id, ci.cantidad, p.id AS producto_id, p.nombre, p.precio, p.imagen, '
    . '(ci.cantidad * p.precio) AS subtotal '
    . 'FROM carritos c JOIN carrito_items ci ON ci.carrito_id = c.id '
    . 'JOIN productos p ON p.id = ci.producto_id '
    . 'WHERE c.usuario_id = ? ORDER BY ci.agregado_en DESC'
);
$consulta->execute([$usuarioId]);
$items = $consulta->fetchAll();
$total = array_sum(array_map(static fn($item) => (float)$item['subtotal'], $items));
if (empty($_SESSION['token_carrito'])) {
    $_SESSION['token_carrito'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token_carrito'];
$estado = $_GET['estado'] ?? '';
$avisos = [
    'agregado' => 'Producto agregado al carrito.',
    'actualizado' => 'Cantidad actualizada.',
    'eliminado' => 'Producto eliminado del carrito.',
    'vacio' => 'Tu carrito está vacío.',
    'cantidad' => 'La cantidad debe estar entre 1 y 99.',
    'sesion' => 'La sesión del formulario venció. Inténtalo de nuevo.',
    'usuario' => 'No se encontró tu cuenta. Inicia sesión nuevamente.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milky | Mi carrito</title>
    <link rel="stylesheet" href="vista/css/estiloSU.css?v=<?= filemtime(__DIR__ . '/vista/css/estiloSU.css') ?>">
    <link rel="stylesheet" href="vista/css/footer.css?v=<?= filemtime(__DIR__ . '/vista/css/footer.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,shopping_cart" rel="stylesheet">
    <style>
        .carrito{width:min(1050px,92%);margin:38px auto 70px;color:#4b3621}
        .carrito h1{color:#7a8744;margin-bottom:22px;text-align:center;font-family:"Bagel Fat One",system-ui;font-weight:400;font-size:clamp(2rem,5vw,3rem)}
        .carrito-aviso,.carrito-vacio,.carrito-total{padding:18px;background:#fff;border-radius:16px;box-shadow:0 5px 18px #00000012;margin-bottom:18px}
        .carrito-aviso{text-align:center;background:#e8f0d5}.carrito-vacio{text-align:center;padding:45px 18px}
        .carrito-fila{display:grid;grid-template-columns:100px minmax(130px,1fr) auto auto auto;align-items:center;gap:18px;background:#fff;padding:16px;margin-bottom:14px;border-radius:16px;box-shadow:0 5px 18px #00000012}
        .carrito-fila img{width:100px;height:95px;object-fit:cover;border-radius:12px}.carrito-fila h2{font-size:1.1rem;color:#7a8744}.carrito-fila p{margin-top:7px}
        .carrito-form{display:flex;align-items:center;gap:8px}.carrito input[type=number]{width:76px;padding:10px;border:1px solid #d8c5a8;border-radius:8px}
        .carrito button,.carrito-enlace{border:0;border-radius:9px;padding:11px 15px;background:#7a8744;color:white;text-decoration:none;cursor:pointer;font:inherit}.carrito .btn-eliminar{background:#a54444}.carrito-total{text-align:right;font-size:1.25rem;font-weight:bold}.carrito-acciones{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
        @media(max-width:720px){.carrito-fila{grid-template-columns:75px 1fr;gap:12px}.carrito-fila img{width:75px;height:75px}.carrito-fila>strong,.carrito-fila>.carrito-form,.carrito-fila>form{grid-column:2}.carrito-fila>.carrito-form{justify-self:start}}
    </style>
</head>
<body>
<header><nav class="menu">
    <div class="fuente"><a href="index.php" aria-label="Ir al inicio"><img src="vista/img/fuente.png" alt="Milky"></a></div>
    <ul class="nav-links"><li><a href="index.php">Inicio</a></li><li><a href="conocenos.php">Nosotros</a></li><li><a href="productosSU.php">Productos</a></li><li><a href="carrito.php" aria-label="Mi carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li></ul>
    <div class="min-logo"><a href="controlador/conexion/cerrar.php" aria-label="Cerrar sesión"><span class="material-symbols-outlined">logout</span></a></div>
</nav></header>
<main class="carrito">
    <h1>Mi carrito de compras</h1>
    <?php if (isset($avisos[$estado])): ?><p class="carrito-aviso" role="status"><?= e($avisos[$estado]) ?></p><?php endif; ?>
    <?php if (!$items): ?>
        <section class="carrito-vacio"><p>Tu carrito todavía no tiene productos.</p><p style="margin-top:18px"><a class="carrito-enlace" href="productosSU.php">Ver productos</a></p></section>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <article class="carrito-fila">
                <img src="vista/img/<?= e($item['imagen']) ?>" alt="<?= e($item['nombre']) ?>">
                <div><h2><?= e($item['nombre']) ?></h2><p>$<?= number_format((float)$item['precio'], 0, ',', '.') ?> por unidad</p></div>
                <strong>$<?= number_format((float)$item['subtotal'], 0, ',', '.') ?></strong>
                <form class="carrito-form" action="controlador/carrito.php" method="post">
                    <input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="accion" value="actualizar"><input type="hidden" name="item_id" value="<?= (int)$item['item_id'] ?>">
                    <label><span class="sr-only">Cantidad de <?= e($item['nombre']) ?></span><input type="number" name="cantidad" min="1" max="99" value="<?= (int)$item['cantidad'] ?>" required></label><button type="submit">Actualizar</button>
                </form>
                <form action="controlador/carrito.php" method="post"><input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="item_id" value="<?= (int)$item['item_id'] ?>"><button class="btn-eliminar" type="submit">Eliminar</button></form>
            </article>
        <?php endforeach; ?>
        <div class="carrito-total">Total: $<?= number_format($total, 0, ',', '.') ?></div>
        <div class="carrito-acciones"><a class="carrito-enlace" href="productosSU.php">Seguir comprando</a><form action="controlador/carrito.php" method="post"><input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="accion" value="vaciar"><button class="btn-eliminar" type="submit">Vaciar carrito</button></form></div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/vista/footer.php'; ?>
</body>
</html>
