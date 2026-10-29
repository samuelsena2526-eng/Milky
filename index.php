<?php
session_start();
$esAdministrador = ($_SESSION['rol'] ?? '') === 'Admin';
$sesionIniciada = isset($_SESSION['usuario']);
$nombreAdministrador = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$mostrarBienvenida = $esAdministrador && ($_GET['bienvenida'] ?? '') === '1';
if ($esAdministrador && empty($_SESSION['token_carrusel'])) {
  $_SESSION['token_carrusel'] = bin2hex(random_bytes(32));
}
$tokenCarrusel = $esAdministrador ? $_SESSION['token_carrusel'] : '';

require_once __DIR__ . '/controlador/conexion/conexion.php';
$consultaCarrusel = $conexion->query(
  'SELECT id, ruta, texto_alternativo
   FROM carrusel_imagenes
   ORDER BY orden_visual ASC, id ASC'
);
$imagenesCarrusel = $consultaCarrusel->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vista/css/1.css?v=<?php echo filemtime(__DIR__ . '/vista/css/1.css'); ?>">
    <link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
      <title>Milky</title>
    <link icon rel="shortcut icon" href="./vista/img/logo.png" type="image/x-icon">
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
<style>
  .momentos-milky {
    position: relative;
    top: -327px;
    width: min(100% - 40px, 1120px);
    box-sizing: border-box;
    margin: 0 auto;
    padding: clamp(28px, 5vw, 54px);
    color: #3f321e;
    background-color: #f8f1df;
    background-image: radial-gradient(#d9c89e 0.7px, transparent 0.7px);
    background-size: 18px 18px;
    border: 2px solid #b1bc54;
    border-radius: 24px;
  }

  .momentos-milky__encabezado {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 26px;
  }

  .momentos-milky__etiqueta {
    color: #7a893a;
    font-family: "Bagel Fat One", system-ui;
    font-size: 0.95rem;
  }

  .momentos-milky h2 {
    margin: 6px 0 0;
    color: #70421f;
    font-family: "Bagel Fat One", system-ui;
    font-size: clamp(1.7rem, 3vw, 2.5rem);
    line-height: 1.15;
  }

  .momentos-milky__encabezado p {
    max-width: 330px;
    margin: 0;
    line-height: 1.55;
  }

  .momentos-milky__lista {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
  }

  .momento-milky {
    min-height: 148px;
    padding: 20px;
    border: 1px solid #d6c89f;
    border-radius: 14px;
    background: #fffdf7;
  }

  .momento-milky__numero {
    display: inline-grid;
    width: 34px;
    aspect-ratio: 1;
    place-items: center;
    border-radius: 50%;
    background: #7a893a;
    color: #fffdf7;
    font-size: 0.8rem;
    font-weight: 700;
  }

  .momento-milky h3 {
    margin: 12px 0 6px;
    color: #70421f;
    font-size: 1.18rem;
  }

  .momento-milky p {
    margin: 0;
    line-height: 1.5;
  }

  .momentos-milky__enlace {
    display: inline-block;
    margin-top: 22px;
    padding: 11px 18px;
    border-radius: 999px;
    background: #7a893a;
    color: #fffdf7;
    font-weight: 700;
    text-decoration: none;
    transition: background-color 0.2s ease, transform 0.2s ease;
  }

  .momentos-milky__enlace:hover {
    background: #70421f;
    transform: translateY(-2px);
  }

  .momentos-milky__enlace:focus-visible {
    outline: 3px solid #a45d27;
    outline-offset: 3px;
  }

  @media (max-width: 700px) {
    .momentos-milky {
      width: calc(100% - 24px);
      padding: 24px 18px;
      border-radius: 18px;
    }

    .momentos-milky__encabezado {
      align-items: start;
      flex-direction: column;
      gap: 10px;
    }

    .momentos-milky__lista {
      grid-template-columns: 1fr;
    }

    .momento-milky {
      min-height: auto;
    }
  }
</style>
</head>
<body class="pagina-inicio">
<?php if ($mostrarBienvenida): ?>
  <script>
    alert(<?php echo json_encode("¡Bienvenido, " . ($_SESSION['nombre'] ?? '') . "! Has iniciado sesión como administrador.", JSON_UNESCAPED_UNICODE); ?>);
    window.history.replaceState({}, document.title, "index.php");
  </script>
<?php endif; ?>
<?php if ($esAdministrador): ?>
  <aside class="admin-info" role="status">
    <strong>⭐ Área de administrador</strong>
    <span>Bienvenido, <?php echo $nombreAdministrador; ?>.</span>
  </aside>
<?php endif; ?>
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
    <?php if ($esAdministrador): ?>
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
      <li><a href="<?php echo $sesionIniciada ? 'carrito.php' : 'registro.php'; ?>" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
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

        <!-- ========================================
       Preloader: cubre la página mientras se muestra la animación inicial.
         ========================================-->
   <div id="preloader">
    <img
      src="lottie/Vaca.svg"
      alt="Animación de vaca"
      style="width: 500px; height: 500px; max-width: 90vw; max-height: 90vh;"
    >
  </div> 
  <!-- Fin del preloader. -->
          <!-- ========================================
         Más especificaciones para la animación del principio
         ========================================-->
      <script>
           window.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        const preloader = document.getElementById('preloader');
        preloader.classList.add('hidden');
      }, 3000); // 3000 milisegundos = 3 segundos
    });
      </script>
     <!-- ========================================
         VAca animación
         ========================================-->
<div class="cow">
  <img src="./vista/img/cuerpo.png" alt="cuerpo" class="body">
  <img src="./vista/img/brazo.png" alt="brazo" class="arm">
</div>
<h1 id="ma">Elige bien</h1>
<h1 id="es">Elige bien</h1>
<h1 id="fe">Elige Natural</h1>
<h1 id="da">- Milky</h1>
<h1 id="lin">- Milky</h1>
<div class="goticas-titulo" aria-hidden="true">
  <span class="gota gota-1"></span>
  <span class="gota gota-2"></span>
  <span class="gota gota-3"></span>
  <span class="gota gota-4"></span>
</div>
     <!-- ========================================
         Productos
         ========================================-->
<div class="producto">
  <img src="./vista/img/produc-2.png" alt="producto">
  <span class="sello-producto">100%<br>natural</span>
</div>


<div class="carta">
    <div class="contenido-animado">
      <div class="texto-contenido">
        <span class="titulito">Leche natural Milky</span>
        <h1 class="carta-titulo">Tu Alimento de confianza</h1>
        <p class="carta-descrip">
Frescura y sabor en cada sorbo.
Elaborada con ingredientes seleccionados
para acompañar tus mejores momentos.
</p>
        <p class="carta-il">100% natural</p>
                <p class="titulito">Calidad Milky</p>
</div>
</div>
</div>
<section class="momentos-milky" aria-labelledby="momentos-milky-titulo">
  <div class="momentos-milky__encabezado">
    <div>
      <span class="momentos-milky__etiqueta">Un toque de Milky</span>
      <h2 id="momentos-milky-titulo">Para disfrutar tus momentos</h2>
    </div>
    <p>Un sabor rico puede hacer especial cualquier pausa del día.</p>
  </div>
  <div class="momentos-milky__lista">
    <article class="momento-milky">
      <span class="momento-milky__numero" aria-hidden="true">01</span>
      <h3>En el desayuno</h3>
      <p>Acompaña tus mañanas con algo rico y sencillo.</p>
    </article>
    <article class="momento-milky">
      <span class="momento-milky__numero" aria-hidden="true">02</span>
      <h3>En la merienda</h3>
      <p>Una pausa para recargar energías y seguir disfrutando.</p>
    </article>
    <article class="momento-milky">
      <span class="momento-milky__numero" aria-hidden="true">03</span>
      <h3>En buena compañía</h3>
      <p>Los pequeños momentos saben mejor cuando se comparten.</p>
    </article>
  </div>
  <a class="momentos-milky__enlace" href="#inspiracion-milky">Ver inspiración</a>
</section>
    <!-- ========================================
         Carrusel de imágenes
         ========================================-->
<div class="carrusel" id="inspiracion-milky">
  <div class="slides">
    <?php for ($grupoCarrusel = 0; $grupoCarrusel < 2; $grupoCarrusel++): ?>
      <?php foreach ($imagenesCarrusel as $imagenCarrusel): ?>
        <article class="slide-link"<?php echo $grupoCarrusel === 1 ? ' aria-hidden="true"' : ''; ?>>
          <img src="<?php echo htmlspecialchars($imagenCarrusel['ruta'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($imagenCarrusel['texto_alternativo'], ENT_QUOTES, 'UTF-8'); ?>">
          <?php if ($esAdministrador && $grupoCarrusel === 0): ?>
            <div class="slide-admin-controls">
              <a class="slide-admin-control" href="controlador/maria/carrusel.php#imagen-<?php echo (int) $imagenCarrusel['id']; ?>" aria-label="Editar anuncio <?php echo (int) $imagenCarrusel['id']; ?>">Editar</a>
              <form method="post" action="controlador/maria/carrusel.php" onsubmit="return confirm('¿Eliminar este anuncio del carrusel?');">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenCarrusel, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?php echo (int) $imagenCarrusel['id']; ?>">
                <button class="slide-admin-control" type="submit" aria-label="Eliminar anuncio <?php echo (int) $imagenCarrusel['id']; ?>">×</button>
              </form>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
      <?php if ($esAdministrador): ?>
        <a class="slide-link slide-add-link" href="controlador/maria/carrusel.php#nuevo"<?php echo $grupoCarrusel === 1 ? ' aria-hidden="true" tabindex="-1"' : ''; ?>>
          <span class="slide-add-link__mas" aria-hidden="true">+</span>
          <span>Modificar carrusel</span>
        </a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>
</div>
<?php require __DIR__ . '/vista/footer.php'; ?>
</body>
</html>
