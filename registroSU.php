<?php
session_start();
// Formulario para crear una cuenta de cliente.
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Milky</title>
    <link rel="stylesheet" href="vista/css/1.css?v=<?php echo filemtime(__DIR__ . '/vista/css/1.css'); ?>">
    <link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">
    <style>
        .login-box { max-width:480px; margin:70px auto; background:#fff; padding:35px; border-radius:25px; box-shadow:0 10px 25px rgba(0,0,0,.15); }
        .login-box h2 { color:#7a8744; text-align:center; margin-bottom:25px; }
        .login-box label { display:block; font-weight:bold; margin-top:15px; }
        .login-box input { width:100%; padding:14px; margin-top:8px; border:2px solid #d98ca2; border-radius:12px; }
        .login-box button { width:100%; margin-top:25px; padding:14px; border:0; border-radius:40px; background:#d98ca2; color:#fff; font-size:17px; cursor:pointer; }
        .error { margin:15px 0; padding:12px; border-radius:12px; background:#f8d7da; }
        .centrado { text-align:center; margin-top:20px; }
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
      <li><a href="<?php echo isset($_SESSION['usuario']) ? 'carrito.php' : 'login.php'; ?>" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
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

<div class="login-box">
    <h2>Crear cuenta</h2>

    <?php if ($error === 'duplicado'): ?>
        <div class="error">Ese nombre ya existe.</div>
    <?php elseif ($error === 'campos'): ?>
        <div class="error">Completa todos los campos.</div>
    <?php endif; ?>

    <form action="controlador/registro.php" method="post">
        <label for="nombre">Nombre</label>
        <input id="nombre" type="text" name="nombre" required>

        <label for="contraseña">Contraseña</label>
        <input id="contraseña" type="password" name="contraseña" required minlength="4">

        <button type="submit">Registrarme</button>
    </form>

    <p class="centrado">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
</div>
<?php require __DIR__ . '/vista/footer.php'; ?>
</body>
</html>
