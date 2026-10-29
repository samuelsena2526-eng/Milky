<?php
require_once __DIR__ . "/controlador/auth.php";
$esAdmin = esAdmin();
$productos = $pdo->query("SELECT * FROM productos ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milky | Catálogo</title>
    <link rel="stylesheet" href="vista/css/estiloSU.css?v=<?php echo filemtime(__DIR__ . '/vista/css/estiloSU.css'); ?> ">
    <link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amarante&family=Bagel+Fat+One&family=Bodoni+Moda:opsz,wght@6..96,400&family=Peralta&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">
    <style>
        .aviso-compra { width:90%; margin:25px auto; padding:16px; border-radius:15px; background:#e8f0d5; color:#4b3621; text-align:center; }
        .producto form { display:flex; flex-direction:column; margin-top:auto; }
        .producto form > button[type="submit"] { min-height:54px; display:flex; align-items:center; justify-content:center; }
        .cantidad-control { display:flex; align-items:center; justify-content:space-between; width:80%; margin:14px auto 0; border:1px solid #d8c5a8; border-radius:10px; overflow:hidden; }
        .cantidad-control input { flex:1; min-width:0; padding:10px 4px; border:0; color:#4b3621; background:transparent; font:inherit; font-weight:600; text-align:center; }
        .cantidad-control input:focus { outline:2px solid #7a8744; outline-offset:-2px; }
        .cantidad-control button { flex:0 0 44px; width:44px; height:42px; margin:0; padding:0; border:0; border-radius:0; background:#f2f4e8; color:#657236; font-size:24px; line-height:1; cursor:pointer; }
        .cantidad-control button:hover { background:#e3e9cf; color:#4b3621; }
        .cantidad-control button:disabled { color:#a9ad98; cursor:not-allowed; }
        .sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
        .admin-crud { width:90%; margin:0 auto 35px; padding:24px; border-radius:16px; background:#fff; box-shadow:0 8px 22px rgba(0,0,0,.1); }
        .admin-crud h3 { margin-bottom:16px; color:#7a8744; }
        .admin-crud form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
        .admin-crud label { display:grid; gap:6px; color:#4b3621; }
        .admin-crud input,.admin-crud select,.admin-crud textarea { width:100%; padding:11px; border:1px solid #d8c5a8; border-radius:8px; background:#fff; color:#4b3621; }
        .admin-crud textarea { min-height:44px; resize:vertical; }
        .admin-crud button,.admin-crud a { align-self:end; justify-self:start; padding:12px 18px; border:0; border-radius:8px; background:#7a8744; color:#fff; text-decoration:none; cursor:pointer; }
        .admin-crud .crud-aviso { grid-column:1/-1; margin:0; padding:10px 12px; border-radius:8px; background:#e8f0d5; }
        @media(max-width:650px) { .admin-crud form { grid-template-columns:1fr; } .admin-crud .crud-aviso { grid-column:auto; } }
    </style>
</head>
<body>
<header>
    <nav class="menu">
        <div class="fuente">
            <a href="index.php" aria-label="Ir al inicio">
                <img src="vista/img/fuente.png" alt="Milky">
            </a>
        </div>
        <?php if (($_SESSION['rol'] ?? '') === 'Admin'): ?>
        <div class="logo">
                <a href="controlador/mariangel/personas_registradas.php" aria-label="Ver cuentas">
                    <span class="material-symbols-outlined" aria-hidden="true">person_text</span>
                </a>
        </div>
        <?php endif; ?>
        <ul class="nav-links">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="conocenos.php">Nosotros</a></li>
            <li><a href="productosSU.php">Productos</a></li>
            <li><a href="conocenos.php#preguntas">Comentarios</a></li>
            <li><a href="<?php echo isset($_SESSION['usuario']) ? 'carrito.php' : 'registro.php'; ?>" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
        </ul>
        <div class="min-logo">
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="controlador/conexion/cerrar.php" aria-label="Cerrar sesión">
                    <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                </a>
            <?php else: ?>
                <a href="registro.php" aria-label="Ir al registro">
                    <img src="vista/img/mini-logo.png" alt="Registrarse">
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<section class="titulo" id="productos">
    <h2>Nuestro Catálogo</h2>
    <p>Productos lácteos elaborados con ingredientes naturales y mucho amor.</p>
</section>

<?php if (($_GET['estado'] ?? '') === 'datos'): ?>
    <p class="aviso-compra" role="alert">Indica una cantidad válida entre 1 y 99.</p>
<?php elseif (($_GET['estado'] ?? '') === 'no_encontrado'): ?>
    <p class="aviso-compra" role="alert">Ese producto ya no está disponible.</p>
<?php endif; ?>

<?php if ($esAdmin): ?>
    <section class="admin-crud" aria-labelledby="agregar-producto">
        <h3 id="agregar-producto">Agregar producto al catálogo</h3>
        <form action="controlador/productos.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="crear">
            <input type="hidden" name="origen" value="catalogo">

            <?php if (($_GET['mensaje'] ?? '') === 'creado'): ?>
                <p class="crud-aviso" role="status">Producto agregado correctamente.</p>
            <?php elseif (($_GET['error'] ?? '') === 'datos'): ?>
                <p class="crud-aviso" role="alert">Revisa el nombre, el precio y la descripción.</p>
            <?php elseif (($_GET['error'] ?? '') === 'imagen'): ?>
                <p class="crud-aviso" role="alert">Sube una imagen JPG, PNG o WEBP de máximo 5 MB.</p>
            <?php endif; ?>

            <label>Nombre
                <input type="text" name="nombre" maxlength="150" required>
            </label>
            <label>Precio
                <input type="number" name="precio" min="1" step="0.01" required>
            </label>
            <label>Descripción
                <textarea name="descripcion" required></textarea>
            </label>
            <label>Imagen del computador
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" required>
            </label>
            <button type="submit">Agregar producto</button>
            <a href="admin/productos.php">Administrar productos</a>
        </form>
    </section>
<?php endif; ?>


<section class="catalogo">
    <?php if ($productos): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="producto">
                <img src="vista/img/<?= e($producto['imagen']) ?>" alt="<?= e($producto['nombre']) ?>">
                <h3><?= e($producto['nombre']) ?></h3>
                <h4>$<?= number_format((float)$producto['precio'], 0, ',', '.') ?></h4>
                <p><?= e($producto['descripcion']) ?></p>

                <form action="controlador/carrito.php" method="post">
                    <input type="hidden" name="token" value="<?= e($_SESSION['token_carrito'] ??= bin2hex(random_bytes(32))) ?>">
                    <input type="hidden" name="accion" value="agregar">
                    <input type="hidden" name="producto_id" value="<?= (int)$producto['id'] ?>">
                    <label class="sr-only" for="cantidad-<?= (int)$producto['id'] ?>">Cantidad</label>
                    <div class="cantidad-control">
                        <button type="button" data-cantidad-cambio="1" aria-label="Aumentar cantidad">+</button>
                        <input id="cantidad-<?= (int)$producto['id'] ?>" type="number" name="cantidad" min="1" max="99" value="1" readonly required>
                        <button type="button" data-cantidad-cambio="-1" aria-label="Disminuir cantidad">−</button>
                    </div>
                    <button type="submit">Agregar al carrito</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="aviso-compra">Todavía no hay productos disponibles.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . "/vista/footer.php"; ?>
<script>
document.querySelectorAll(".cantidad-control").forEach((control) => {
    const cantidad = control.querySelector('input[name="cantidad"]');
    const botones = control.querySelectorAll("[data-cantidad-cambio]");

    const actualizarBotones = () => {
        botones.forEach((boton) => {
            const cambio = Number(boton.dataset.cantidadCambio);
            boton.disabled = cambio > 0
                ? Number(cantidad.value) >= Number(cantidad.max)
                : Number(cantidad.value) <= Number(cantidad.min);
        });
    };

    botones.forEach((boton) => {
        boton.addEventListener("click", () => {
            const minimo = Number(cantidad.min);
            const maximo = Number(cantidad.max);
            const cambio = Number(boton.dataset.cantidadCambio);
            cantidad.value = String(Math.min(maximo, Math.max(minimo, Number(cantidad.value) + cambio)));
            actualizarBotones();
        });
    });

    actualizarBotones();
});
</script>
</body>
</html>
