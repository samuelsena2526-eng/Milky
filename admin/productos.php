<?php
// Vista del CRUD de productos.
// Solo los administradores pueden entrar aquí.

require_once __DIR__ . "/../controlador/conexion.php";
require_once __DIR__ . "/../controlador/auth.php";

requerirAdmin();

$editar = null;

if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];

    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$idEditar]);
    $editar = $stmt->fetch();
}

$productos = $pdo->query(
    "SELECT * FROM productos ORDER BY id ASC"
)->fetchAll();

$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Productos | Milky</title>
    <link rel="stylesheet" href="../vista/css/1.css?v=<?php echo filemtime(__DIR__ . '/../vista/css/1.css'); ?>">
    <link rel="stylesheet" href="../vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/../vista/css/footer.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">
    <style>
        .crud-contenedor{width:92%;max-width:1200px;margin:50px auto}
        .crud-card{background:#fff;padding:30px;border-radius:25px;box-shadow:0 10px 25px rgba(0,0,0,.12);margin-bottom:35px}
        .crud-card h2{color:#7a8744;margin-bottom:20px}
        .crud-form{display:grid;gap:12px}
        .crud-form input,.crud-form textarea{padding:12px;border:2px solid #d98ca2;border-radius:12px}
        .crud-form button,.btn{border:0;border-radius:30px;padding:12px 20px;background:#d98ca2;color:#fff;text-decoration:none;cursor:pointer;display:inline-block}
        .btn-verde{background:#7a8744}
        .btn-rojo{background:#b85c6f}
        table{width:100%;border-collapse:collapse}
        th,td{padding:12px;border-bottom:1px solid #ddd;text-align:left}
        td img{width:70px;height:70px;object-fit:cover;border-radius:12px}
        .acciones{display:flex;gap:8px;flex-wrap:wrap}
        .aviso{padding:14px;border-radius:12px;background:#e8f0d5;margin-bottom:20px}
        .error{padding:14px;border-radius:12px;background:#f8d7da;margin-bottom:20px}
        @media(max-width:800px){table{font-size:13px}.crud-contenedor{width:96%}}
    </style>
</head>
<body>
<header>
    <nav class="menu">
        <div class="fuente">
            <a href="../index.php" aria-label="Ir al inicio"><img src="../vista/img/fuente.png" alt="Milky"></a>
        </div>
        <div class="logo">
            <a href="../controlador/mariangel/personas_registradas.php" aria-label="Ver cuentas"><span class="material-symbols-outlined" aria-hidden="true">person_text</span></a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="../conocenos.php">Nosotros</a></li>
            <li><a href="../productosSU.php">Productos</a></li>
            <li><a href="../conocenos.php#preguntas">Comentarios</a></li>
            <li><a href="../carrito.php" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
        </ul>
        <div class="min-logo">
            <a href="../controlador/conexion/cerrar.php" aria-label="Cerrar sesión"><span class="material-symbols-outlined" aria-hidden="true">logout</span></a>
        </div>
    </nav>
</header>

<main class="crud-contenedor">
    <?php if ($mensaje): ?>
        <div class="aviso">
            Operación realizada correctamente.
        </div>
    <?php endif; ?>

    <?php if ($error === 'datos'): ?>
        <div class="error">
            Revisa los datos del producto.
        </div>
    <?php elseif ($error === 'en_carrito'): ?>
        <div class="error" role="alert">
            No se puede eliminar este producto porque todavía está en el carrito de un cliente.
        </div>
    <?php endif; ?>

    <section class="crud-card">
        <h2><?= $editar ? 'Actualizar producto' : 'Crear producto' ?></h2>

        <?php if ($error === 'imagen'): ?>
            <div class="error" role="alert">Sube una imagen JPG, PNG o WEBP de máximo 5 MB.</div>
        <?php endif; ?>

        <form class="crud-form" action="../controlador/productos.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">

            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= (int)$editar['id'] ?>">
            <?php endif; ?>

            <label>Nombre</label>
            <input type="text" name="nombre" required value="<?= e($editar['nombre'] ?? '') ?>">

            <label>Precio</label>
            <input type="number" name="precio" min="1" step="0.01" required value="<?= e($editar['precio'] ?? '') ?>">

            <label>Descripción</label>
            <textarea name="descripcion" rows="4" required><?= e($editar['descripcion'] ?? '') ?></textarea>

            <?php if ($editar): ?>
                <img src="../vista/img/<?= e($editar['imagen']) ?>" alt="Imagen actual de <?= e($editar['nombre']) ?>" width="100" height="100" style="object-fit:cover;border-radius:8px">
            <?php endif; ?>

            <label for="imagen">Imagen desde el computador<?= $editar ? ' (opcional para conservar la actual)' : '' ?></label>
            <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp" <?= $editar ? '' : 'required' ?>>

            <div>
                <button type="submit">
                    <?= $editar ? 'Actualizar' : 'Guardar producto' ?>
                </button>

                <?php if ($editar): ?>
                    <a class="btn btn-verde" href="productos.php">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="crud-card">
        <h2>Productos registrados</h2>

        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>
                        <img src="../vista/img/<?= e($producto['imagen']) ?>" alt="<?= e($producto['nombre']) ?>">
                    </td>
                    <td><?= e($producto['nombre']) ?></td>
                    <td>$<?= number_format((float)$producto['precio'], 0, ',', '.') ?></td>
                    <td>
                        <div class="acciones">
                            <a class="btn btn-verde" href="productos.php?editar=<?= (int)$producto['id'] ?>">Editar</a>

                            <a class="btn btn-rojo"
                               href="../controlador/productos.php?accion=eliminar&id=<?= (int)$producto['id'] ?>"
                               onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                                Eliminar
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<?php require __DIR__ . '/../vista/footer.php'; ?>
</body>
</html>
