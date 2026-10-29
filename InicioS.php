<?php
session_start();
$sesionIniciada = isset($_SESSION['usuario']);
$error = $_GET['error'] ?? '';
$registroOk = ($_GET['registro'] ?? '') === 'ok';
?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link icon rel="shortcut icon" href="./vista/img/logo.png" type="image/x-icon">

<title>Iniciar Sesión</title>   <!-- Título de la página -->

 <!-- ========================================
          Link para la animación del principio 
          ========================================-->
           <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
     <!-- ========================================
          Fuentes de letra googleee 👽
          ========================================-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&family=Fascinate+Inline&family=Oi&family=Rubik+Spray+Paint&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">
     <!-- ========================================
          CSS
          ========================================-->
          <link rel="stylesheet" href="vista/css/inicioS.css?v=<?php echo filemtime(__DIR__ . '/vista/css/inicioS.css'); ?>">
          <link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
<style>
.login-aviso { margin: 0 auto 16px; padding: 10px 14px; border-radius: 10px; }
.login-error { color: #842029; background: #f8d7da; }
.login-ok { color: #0f5132; background: #d1e7dd; }
</style>
</head>

<body>
   
         <!-- ========================================
         El navegador de la página.
         ========================================-->
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
      <li><a href="<?php echo $sesionIniciada ? 'carrito.php' : 'login.php'; ?>" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
    </ul>
      <div class="min-logo">
      <?php if ($sesionIniciada): ?>
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

<div class="vaca"> 
    <img src="vista/img/vaca.png" alt="vista/img/vaca.png" class="vaca-imagen"> <!-- Imagen de una vaca -->
</div>

<div class="contenedor"> <!-- Contenedor principal -->

<h2>Inicio de Sesión</h2>  <!-- Título de la sección -->

<?php if ($registroOk): ?>
<p class="login-aviso login-ok" role="status">Cuenta creada correctamente. Ya puedes iniciar sesión.</p>
<?php endif; ?>
<?php if ($error === 'credenciales'): ?>
<p class="login-aviso login-error" role="alert">Correo o contraseña incorrectos.</p>
<?php elseif ($error === 'hash_incompleto'): ?>
<p class="login-aviso login-error" role="alert">La contraseña guardada quedó incompleta por el límite anterior de la base de datos. Esta cuenta necesita que se restablezca su contraseña.</p>
<?php elseif ($error === 'campos'): ?>
<p class="login-aviso login-error" role="alert">Completa todos los campos.</p>
<?php endif; ?>

<form action="controlador/mariangel/validar.php" method="POST">    <!-- Formulario para iniciar sesión -->

<input type="email" name="correo" placeholder="Correo" required> <!-- Campo para ingresar el correo -->

<input type="password" name="contraseña" placeholder="Contraseña" autocomplete="current-password" required>

<button type="submit">Ingresar</button> <!-- Botón para enviar el formulario -->

</form>
<p>

¿No tienes cuenta?  <!-- Pregunta para los usuarios que no tienen cuenta -->    

<a href="registro.php">Regístrate</a> <!-- Enlace para ir a la página de registro -->

</p>

</div>
</body>

</html>
