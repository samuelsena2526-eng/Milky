<?php
session_start();
$registroExitoso = isset($_GET['registrado']) && $_GET['registrado'] === '1';
$compraRequerida = isset($_GET['compra']) && $_GET['compra'] === '1';
$sesionIniciada = isset($_SESSION['usuario']);
$errorRegistro = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link icon rel="shortcut icon" href="./vista/img/logo.png" type="image/x-icon">

<title>Registro</title>

<link rel="stylesheet" href="vista/css/inicioS.css?v=<?php echo filemtime(__DIR__ . '/vista/css/inicioS.css'); ?>">
<link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">

</head>

<body class="registro-page">

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
	<img src="vista/img/vaca.png" alt="vista/img/vaca.png" class="vaca-imagen">
</div>

<div class="contenedor"> <!-- Contenedor principal -->

<?php if ($registroExitoso): ?>
<div class="notificacion-exito" role="alert">🐮 ¡Registrado correctamente!</div>
<?php endif; ?>
<?php if ($compraRequerida): ?>
<div class="notificacion-exito" role="status">🐮 Debes crear una cuenta para comprar.</div>
<?php endif; ?>
<?php if ($errorRegistro !== ''): ?>
<div class="notificacion-error" role="alert">
	<?php if ($errorRegistro === 'duplicado'): ?>Ese correo ya está registrado.
	<?php elseif ($errorRegistro === 'campos'): ?>Completa todos los campos.
	<?php elseif ($errorRegistro === 'datos'): ?>Revisa el nombre y el formato del correo.
	<?php else: ?>No se pudo completar el registro. Inténtalo de nuevo.
	<?php endif; ?>
</div>
<?php endif; ?>

<h1>Registrarse</h1>  <!-- Título de la sección -->

<form action="controlador/mariangel/guardar.php" method="POST"> <!-- Formulario para registrarse -->


<input type="text" name="nombre" placeholder="Nombre" required> <!-- Campo para ingresar el nombre -->


<input type="email" name="correo" placeholder="Correo" required> <!-- Campo para ingresar el correo -->


<input type="password" name="contraseña" placeholder="Contraseña" autocomplete="new-password" required minlength="4">

<button type="submit">Registrarse</button> <!-- Botón para enviar el formulario -->

</form>
<p>

¿Ya tienes cuenta? <!-- Pregunta para los usuarios que ya tienen cuenta -->

<a href="InicioS.php">Inicia sesión</a>  <!-- Enlace para ir a la página de inicio de sesión -->

</p>



</div>

</body>

</html>
