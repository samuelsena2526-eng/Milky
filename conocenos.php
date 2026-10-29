<?php
session_start();
$esAdministrador = ($_SESSION['rol'] ?? '') === 'Admin';
$sesionIniciada = isset($_SESSION['usuario']);
?>
<!DOCTYPE html> <!-- Declaración del tipo de documento HTML -->
<html lang="es"> <!-- Inicio del documento HTML con el idioma español -->

<head> <!-- Inicio de la cabecera del documento -->
    <meta charset="UTF-8"> <!-- Configuración de codificación de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración para diseño responsivo -->

    <title>Conócenos | Milky</title> <!-- Título de la página -->

    <!-- Conexión con el archivo CSS -->
    <link rel="stylesheet" href="vista/css/estiloMA.CSS?v=<?php echo filemtime(__DIR__ . '/vista/css/estiloMA.CSS'); ?>"> <!-- Enlace al archivo de estilos CSS -->
    <link rel="stylesheet" href="vista/css/1.css?v=<?php echo filemtime(__DIR__ . '/vista/css/1.css'); ?>"> <!-- Estilos del menú principal -->
    <link rel="stylesheet" href="vista/css/footer.css?v=<?php echo filemtime(__DIR__ . '/vista/css/footer.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Preconexión a Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <!-- Preconexión a Google Fonts con atributo crossorigin -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet"> <!-- Enlace a una fuente de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=logout,person_text,shopping_cart&display=block" rel="stylesheet">
</head> <!-- Fin de la cabecera del documento -->

<body> <!-- Inicio del cuerpo del documento -->

    <header>
        <nav class="menu" aria-label="Navegación principal">
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
                <li><a href="conocenos.php" aria-current="page">Nosotros</a></li>
                <li><a href="productosSU.php">Productos</a></li>
                <li><a href="conocenos.php#preguntas">Comentarios</a></li>
                <li><a href="carrito.php" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
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

    <!-- PORTADA -->
    <div class="encabezado" aria-label="Portada de Milky"></div>

    <!-- CONTENIDO -->
    <main class="contenedor"> <!-- Contenedor principal del contenido -->

        <!-- NUESTRO PROPÓSITO -->
        <section class="tarjeta"> <!-- Sección para "Nuestro propósito" -->

            <div class="icono verde"> <!-- Contenedor del ícono con clase "verde" -->
                🥛 <!-- Ícono representativo -->
            </div>

            <div class="texto"> <!-- Contenedor del texto -->

                <h2>Nuestro propósito ❤️</h2> <!-- Título de la sección -->

                <p>
                    Alimentamos el bienestar de cada familia
                    con productos nutritivos elaborados bajo
                    altos estándares de calidad.
                </p> <!-- Descripción del propósito -->

            </div>

        </section> <!-- Fin de la sección "Nuestro propósito" -->

        <!-- NUESTRA HISTORIA -->
        <section class="tarjeta"> <!-- Sección para "Nuestra historia" -->

            <div class="icono rosado"> <!-- Contenedor del ícono con clase "rosado" -->
                🐮 <!-- Ícono representativo -->
            </div>

            <div class="texto"> <!-- Contenedor del texto -->

                <h2>Nuestra historia 💚</h2> <!-- Título de la sección -->

                <p>
                    Desde nuestros inicios, hemos trabajado
                    con pasión y compromiso para llevar a tu
                    mesa lo mejor de nuestra tierra.
                </p> <!-- Descripción de la historia -->

            </div>

        </section> <!-- Fin de la sección "Nuestra historia" -->

        <!-- SOSTENIBILIDAD -->
        <section class="tarjeta"> <!-- Sección para "Sostenibilidad" -->

            <div class="icono verde"> <!-- Contenedor del ícono con clase "verde" -->
                🌿 <!-- Ícono representativo -->
            </div>

            <div class="texto"> <!-- Contenedor del texto -->

                <h2>Sostenibilidad 🌱</h2> <!-- Título de la sección -->

                <p>
                    Trabajamos de la mano con productores
                    locales y cuidamos el medio ambiente
                    mediante procesos responsables.
                </p> <!-- Descripción de sostenibilidad -->

            </div>

        </section> <!-- Fin de la sección "Sostenibilidad" -->

        <!-- NUESTRA CULTURA -->
        <section class="tarjeta"> <!-- Sección para "Nuestra cultura" -->

            <div class="icono rosado"> <!-- Contenedor del ícono con clase "rosado" -->
                🤍 <!-- Ícono representativo -->
            </div>

            <div class="texto"> <!-- Contenedor del texto -->

                <h2>Nuestra cultura</h2> <!-- Título de la sección -->

                <p>
                    Nuestro equipo trabaja con compromiso,
                    respeto, innovación y pasión por entregar
                    siempre lo mejor.
                </p> <!-- Descripción de la cultura -->

            </div>

        </section> <!-- Fin de la sección "Nuestra cultura" -->

    </main> <!-- Fin del contenedor principal -->

    <?php include_once __DIR__ . "/pregunta.php"; ?>
    <?php require __DIR__ . "/vista/footer.php"; ?>

</body> <!-- Fin del cuerpo del documento -->

</html> <!-- Fin del documento HTML -->
