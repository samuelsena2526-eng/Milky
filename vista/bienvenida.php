<?php

session_start(); // Iniciamos la sesión

if(!isset($_SESSION['usuario'])){ // Verificamos si el usuario ha iniciado sesión

header("Location: ../index.php"); // Si no ha iniciado sesión, lo redirigimos al inicio
exit;

}

$nombre = htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$esAdministrador = ($_SESSION['rol'] ?? '') === 'Admin';

?> 

<!DOCTYPE html>

<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/inicioS.css?v=<?php echo filemtime(__DIR__ . '/css/inicioS.css'); ?>"> <!-- Enlace al archivo de estilos -->
<link rel="stylesheet" href="css/footer.css?v=<?php echo filemtime(__DIR__ . '/css/footer.css'); ?>">
<link href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&family=Fascinate+Inline&family=Oi&family=Rubik+Spray+Paint&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=logout,person_text,shopping_cart" rel="stylesheet">

</head>

<body>

<header>
	<nav class="menu">
		<div class="fuente">
			<a href="../index.php" aria-label="Ir al inicio">
				<img src="img/fuente.png" alt="Milky">
			</a>
		</div>
		<?php if ($esAdministrador): ?>
		<div class="logo">
				<a href="../controlador/mariangel/personas_registradas.php" aria-label="Ver cuentas">
					<span class="material-symbols-outlined" aria-hidden="true">person_text</span>
				</a>
		</div>
		<?php endif; ?>
		<ul class="nav-links">
			<li><a href="../index.php">Inicio</a></li>
			<li><a href="../conocenos.php">Nosotros</a></li>
			<li><a href="../productosSU.php">Productos</a></li>
			<li><a href="../conocenos.php#preguntas">Comentarios</a></li>
			<li><a href="../carrito.php" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
		</ul>
		<div class="min-logo">
			<a href="../controlador/conexion/cerrar.php" aria-label="Cerrar sesión">
				<span class="material-symbols-outlined" aria-hidden="true">logout</span>
			</a>
		</div>
	</nav>
</header>

<div class="contenedor"> <!-- Contenedor principal -->

<h2>Bienvenido, <?php echo $nombre; ?></h2> <!-- Título de la página -->

<?php if ($esAdministrador): ?>
<p>⭐ Has iniciado sesión como administrador.</p>
<?php else: ?>
<p>Has iniciado sesión correctamente.</p>
<?php endif; ?>

<a href="../controlador/conexion/cerrar.php"> <!-- Enlace para cerrar sesión -->

<button>Cerrar sesión</button> <!-- Botón para cerrar sesión -->

</a>

</div>
<?php require __DIR__ . '/footer.php'; ?>

</body>

</html>
