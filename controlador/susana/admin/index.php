<?php
// Panel principal del administrador.
require_once __DIR__ . "/../autenticacion.php";
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Milky</title>
    <link rel="stylesheet" href="../../../vista/css/estiloSU.css">
    <link rel="stylesheet" href="../../../vista/css/1.css?v=<?php echo filemtime(__DIR__ . '/../../../vista/css/1.css'); ?>">
    <link rel="stylesheet" href="../../../vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/../../../vista/css/footer.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">
    <style>
        .admin-box{max-width:900px;margin:70px auto;padding:40px;background:#fff;border-radius:25px;box-shadow:0 10px 25px rgba(0,0,0,.15);text-align:center}
        .admin-box a{display:inline-block;margin:12px;padding:15px 30px;border-radius:40px;background:#d98ca2;color:#fff;text-decoration:none}
        .admin-box a:hover{background:#7a8744}
    </style>
</head>
<body>
<header>
    <nav class="menu">
        <div class="fuente">
            <a href="../../../index.php" aria-label="Ir al inicio"><img src="../../../vista/img/fuente.png" alt="Milky"></a>
        </div>
        <div class="logo">
            <a href="../../mariangel/personas_registradas.php" aria-label="Ver cuentas"><span class="material-symbols-outlined" aria-hidden="true">person_text</span></a>
        </div>
        <ul class="nav-links">
            <li><a href="../../../index.php">Inicio</a></li>
            <li><a href="../../../conocenos.php">Nosotros</a></li>
            <li><a href="../../../productosSU.php">Productos</a></li>
      <li><a href="../../../conocenos.php#preguntas">Comentarios</a></li>
      <li><a href="../../../carrito.php" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
        </ul>
        <div class="min-logo">
            <a href="../../conexion/cerrar.php" aria-label="Cerrar sesión"><span class="material-symbols-outlined" aria-hidden="true">logout</span></a>
        </div>
    </nav>
</header>
<div class="admin-box">
    <h2>Panel de administración</h2>
    <p>Desde aquí puedes administrar los productos del catálogo.</p>
    <a href="productos.php">CRUD de productos</a>
    <a href="../../../productosSU.php">Ver catálogo</a>
</div>
<?php require __DIR__ . '/../../../vista/footer.php'; ?>
</body>
</html>
