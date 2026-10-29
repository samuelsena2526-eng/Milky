<?php
require_once __DIR__ . '/../auth.php';

if (!esAdmin()) {
    header('Location: ../../index.php');
    exit;
}

$usuarioId = obtenerUsuarioSesionId();

if (empty($_SESSION['token_carrusel'])) {
    $_SESSION['token_carrusel'] = bin2hex(random_bytes(32));
}

$tokenCarrusel = $_SESSION['token_carrusel'];
$directorioImagenes = dirname(__DIR__, 2) . '/vista/img/publiciadad';
$error = '';

function escaparCarrusel($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function guardarImagenCarrusel($archivo, $directorio, $obligatoria)
{
    if (!isset($archivo) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        if ($obligatoria) {
            throw new RuntimeException('Selecciona una imagen para el anuncio.');
        }

        return null;
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo recibir la imagen. Intenta subirla de nuevo.');
    }

    if ($archivo['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('La imagen debe pesar máximo 5 MB.');
    }

    $informacionImagen = @getimagesize($archivo['tmp_name']);
    $tipoMime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
    $extensionesPermitidas = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!$informacionImagen || !isset($extensionesPermitidas[$tipoMime])) {
        throw new RuntimeException('Usa una imagen JPG, PNG o WebP válida.');
    }

    $nombreArchivo = 'carrusel-' . bin2hex(random_bytes(16)) . '.' . $extensionesPermitidas[$tipoMime];
    $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        throw new RuntimeException('No se pudo guardar la imagen en el servidor.');
    }

    return 'vista/img/publiciadad/' . $nombreArchivo;
}

function eliminarImagenSubidaCarrusel($ruta)
{
    $nombreArchivo = basename((string) $ruta);

    if (!preg_match('/^carrusel-[a-f0-9]{32}\.(jpg|png|webp)$/', $nombreArchivo)) {
        return;
    }

    $rutaArchivo = dirname(__DIR__, 2) . '/vista/img/publiciadad/' . $nombreArchivo;
    if (is_file($rutaArchivo)) {
        unlink($rutaArchivo);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagenNueva = null;

    try {
        if (!hash_equals($tokenCarrusel, $_POST['token'] ?? '')) {
            throw new RuntimeException('La sesión del formulario venció. Recarga la página e inténtalo otra vez.');
        }

        $accion = $_POST['accion'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($accion === 'eliminar') {
            if (!$id) {
                throw new RuntimeException('El anuncio indicado no es válido.');
            }

            $consulta = $conexion->prepare('SELECT ruta FROM carrusel_imagenes WHERE id = :id');
            $consulta->execute([':id' => $id]);
            $anuncio = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$anuncio) {
                throw new RuntimeException('No se encontró ese anuncio.');
            }

            $consulta = $conexion->prepare('DELETE FROM carrusel_imagenes WHERE id = :id');
            $consulta->execute([':id' => $id]);
            eliminarImagenSubidaCarrusel($anuncio['ruta']);
            $_SESSION['mensaje_carrusel'] = 'Anuncio eliminado del carrusel.';
            header('Location: carrusel.php#lista');
            exit;
        }

        $textoAlternativo = trim($_POST['texto_alternativo'] ?? '');
        $ordenVisual = filter_var($_POST['orden_visual'] ?? null, FILTER_VALIDATE_INT);

        if ($textoAlternativo === '' || strlen($textoAlternativo) > 180) {
            throw new RuntimeException('Escribe una descripción de imagen de hasta 180 caracteres.');
        }

        if ($ordenVisual === false || $ordenVisual < 1 || $ordenVisual > 9999) {
            throw new RuntimeException('El orden debe ser un número entre 1 y 9999.');
        }

        if ($accion === 'crear') {
            if ($usuarioId === null) {
                throw new RuntimeException('No se pudo identificar la cuenta administradora.');
            }

            $imagenNueva = guardarImagenCarrusel($_FILES['imagen'] ?? null, $directorioImagenes, true);
            $consulta = $conexion->prepare(
                'INSERT INTO carrusel_imagenes (ruta, texto_alternativo, orden_visual, creado_por)
                 VALUES (:ruta, :texto_alternativo, :orden_visual, :creado_por)'
            );
            $consulta->execute([
                ':ruta' => $imagenNueva,
                ':texto_alternativo' => $textoAlternativo,
                ':orden_visual' => $ordenVisual,
                ':creado_por' => $usuarioId,
            ]);

            $imagenNueva = null;
            $_SESSION['mensaje_carrusel'] = 'Imagen agregada al carrusel.';
            header('Location: carrusel.php#lista');
            exit;
        }

        if ($accion === 'actualizar') {
            if (!$id) {
                throw new RuntimeException('El anuncio indicado no es válido.');
            }

            $consulta = $conexion->prepare('SELECT ruta FROM carrusel_imagenes WHERE id = :id');
            $consulta->execute([':id' => $id]);
            $anuncio = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$anuncio) {
                throw new RuntimeException('No se encontró ese anuncio.');
            }

            $imagenNueva = guardarImagenCarrusel($_FILES['imagen'] ?? null, $directorioImagenes, false);
            $rutaImagen = $imagenNueva ?? $anuncio['ruta'];
            $consulta = $conexion->prepare(
                'UPDATE carrusel_imagenes
                 SET ruta = :ruta, texto_alternativo = :texto_alternativo, orden_visual = :orden_visual
                 WHERE id = :id'
            );
            $consulta->execute([
                ':ruta' => $rutaImagen,
                ':texto_alternativo' => $textoAlternativo,
                ':orden_visual' => $ordenVisual,
                ':id' => $id,
            ]);

            if ($imagenNueva !== null) {
                eliminarImagenSubidaCarrusel($anuncio['ruta']);
            }

            $imagenNueva = null;
            $_SESSION['mensaje_carrusel'] = 'Anuncio actualizado.';
            header('Location: carrusel.php#imagen-' . $id);
            exit;
        }

        throw new RuntimeException('La acción solicitada no es válida.');
    } catch (Throwable $exception) {
        if ($imagenNueva !== null) {
            eliminarImagenSubidaCarrusel($imagenNueva);
        }

        $error = $exception instanceof RuntimeException
            ? $exception->getMessage()
            : 'No se pudo guardar el cambio. Revisa la base de datos e inténtalo otra vez.';
    }
}

$mensaje = $_SESSION['mensaje_carrusel'] ?? '';
unset($_SESSION['mensaje_carrusel']);

$consulta = $conexion->query(
    'SELECT id, ruta, texto_alternativo, orden_visual
     FROM carrusel_imagenes
     ORDER BY orden_visual ASC, id ASC'
);
$anuncios = $consulta->fetchAll(PDO::FETCH_ASSOC);
$siguienteOrden = $anuncios ? max(array_column($anuncios, 'orden_visual')) + 1 : 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../vista/css/1.css?v=<?php echo filemtime(dirname(__DIR__, 2) . '/vista/css/1.css'); ?>">
    <link rel="stylesheet" href="../../vista/css/footer.css?v=<?php echo filemtime(dirname(__DIR__, 2) . '/vista/css/footer.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=logout,person_text&display=block" rel="stylesheet">
    <title>Administrar carrusel | Milky</title>
    <style>
        :root {
            color-scheme: light;
            --oliva: #71813a;
            --oliva-oscuro: #526229;
            --cafe: #70421f;
            --crema: #f5eedf;
            --papel: #fffdf7;
            --borde: #d8cda9;
            --rojo: #a43d36;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #3f321e;
            background: var(--crema);
            font-family: Georgia, serif;
        }

        .barra-admin {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px clamp(20px, 6vw, 76px);
            color: #fffdf7;
            background: var(--oliva-oscuro);
        }

        .barra-admin strong {
            font-family: Georgia, serif;
            font-size: 1.2rem;
        }

        .barra-admin a { color: inherit; }

        main {
            width: min(1120px, calc(100% - 36px));
            margin: 38px auto 64px;
        }

        h1, h2, h3 { color: var(--cafe); }
        h1 { margin: 0 0 8px; font-size: clamp(1.8rem, 4vw, 2.5rem); }
        h2 { margin: 0 0 18px; font-size: 1.35rem; }

        .introduccion { margin: 0 0 28px; line-height: 1.55; }

        .aviso {
            margin: 0 0 22px;
            padding: 13px 16px;
            border-left: 4px solid var(--oliva);
            background: var(--papel);
        }

        .aviso.error { border-color: var(--rojo); }

        .panel, .anuncio {
            border: 1px solid var(--borde);
            border-radius: 8px;
            background: var(--papel);
        }

        .panel { margin-bottom: 34px; padding: clamp(18px, 3vw, 28px); }

        .formulario-nuevo {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(170px, 0.38fr);
            gap: 16px;
        }

        .campo { display: flex; flex-direction: column; gap: 7px; }
        .campo-ancho { grid-column: 1 / -1; }
        label { font-size: 0.92rem; font-weight: 700; }

        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #b9ad89;
            border-radius: 5px;
            background: white;
            color: #332b1e;
            font: inherit;
        }

        input:focus-visible, button:focus-visible, a:focus-visible {
            outline: 3px solid #a45d27;
            outline-offset: 2px;
        }

        .boton {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 15px;
            border: 0;
            border-radius: 5px;
            background: var(--oliva);
            color: white;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .boton:hover { background: var(--oliva-oscuro); }
        .boton.eliminar { background: var(--rojo); }
        .boton.eliminar:hover { background: #812e28; }

        .lista-anuncios {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
            gap: 18px;
        }

        .anuncio { overflow: hidden; }

        .anuncio img {
            display: block;
            width: 100%;
            height: 210px;
            object-fit: contain;
            background: #eee5d1;
        }

        .anuncio__contenido { padding: 17px; }
        .anuncio__contenido h3 { margin: 0 0 14px; }
        .formulario-edicion { display: grid; gap: 13px; }
        .acciones { display: flex; flex-wrap: wrap; gap: 10px; }
        .ayuda { margin: 6px 0 0; color: #675d4b; font-size: 0.9rem; line-height: 1.45; }
        .vacio { padding: 20px; border: 1px dashed var(--borde); background: var(--papel); }

        @media (max-width: 620px) {
            .barra-admin { align-items: flex-start; flex-direction: column; gap: 8px; }
            .formulario-nuevo { grid-template-columns: 1fr; }
            .campo-ancho { grid-column: auto; }
        }
    </style>
</head>
<body>
    <header>
        <nav class="menu">
            <div class="fuente">
                <a href="../../index.php" aria-label="Ir al inicio">
                    <img src="../../vista/img/fuente.png" alt="Milky">
                </a>
            </div>
            <div class="logo">
                <a href="../../controlador/mariangel/personas_registradas.php" aria-label="Ver cuentas">
                    <span class="material-symbols-outlined" aria-hidden="true">person_text</span>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../../conocenos.php">Nosotros</a></li>
                <li><a href="../../productosSU.php">Productos</a></li>
                <li><a href="../../conocenos.php#preguntas">Comentarios</a></li>
                <li><a href="../../carrito.php" aria-label="Ir al carrito"><span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span></a></li>
            </ul>
            <div class="min-logo">
                <a href="../../controlador/conexion/cerrar.php" aria-label="Cerrar sesión">
                    <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                </a>
            </div>
        </nav>
    </header>
    <main>
        <h1>Administrar carrusel</h1>
        <p class="introduccion">Agrega anuncios, reemplaza sus imágenes, cambia su descripción y ordena cómo aparecen en la portada.</p>

        <?php if ($mensaje): ?>
            <p class="aviso" role="status"><?php echo escaparCarrusel($mensaje); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="aviso error" role="alert"><?php echo escaparCarrusel($error); ?></p>
        <?php endif; ?>

        <section class="panel" id="nuevo" aria-labelledby="nuevo-titulo">
            <h2 id="nuevo-titulo">＋ Agregar anuncio</h2>
            <form class="formulario-nuevo" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo escaparCarrusel($tokenCarrusel); ?>">
                <input type="hidden" name="accion" value="crear">
                <div class="campo">
                    <label for="nueva-imagen">Imagen (JPG, PNG o WebP; máximo 5 MB)</label>
                    <input id="nueva-imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp" required>
                </div>
                <div class="campo">
                    <label for="nuevo-orden">Orden en el carrusel</label>
                    <input id="nuevo-orden" type="number" name="orden_visual" min="1" max="9999" value="<?php echo (int) $siguienteOrden; ?>" required>
                </div>
                <div class="campo campo-ancho">
                    <label for="nuevo-texto">Descripción de la imagen</label>
                    <input id="nuevo-texto" type="text" name="texto_alternativo" maxlength="180" required>
                </div>
                <div class="campo campo-ancho">
                    <button class="boton" type="submit">＋ Agregar al carrusel</button>
                </div>
            </form>
        </section>

        <section id="lista" aria-labelledby="lista-titulo">
            <h2 id="lista-titulo">Anuncios actuales · <?php echo count($anuncios); ?></h2>
            <?php if (!$anuncios): ?>
                <p class="vacio">El carrusel aún no tiene imágenes. Usa “Agregar anuncio” para comenzar.</p>
            <?php else: ?>
                <div class="lista-anuncios">
                    <?php foreach ($anuncios as $anuncio): ?>
                        <article class="anuncio" id="imagen-<?php echo (int) $anuncio['id']; ?>">
                            <img src="../../<?php echo escaparCarrusel($anuncio['ruta']); ?>" alt="<?php echo escaparCarrusel($anuncio['texto_alternativo']); ?>">
                            <div class="anuncio__contenido">
                                <h3>Anuncio #<?php echo (int) $anuncio['id']; ?></h3>
                                <form class="formulario-edicion" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="token" value="<?php echo escaparCarrusel($tokenCarrusel); ?>">
                                    <input type="hidden" name="accion" value="actualizar">
                                    <input type="hidden" name="id" value="<?php echo (int) $anuncio['id']; ?>">
                                    <div class="campo">
                                        <label for="texto-<?php echo (int) $anuncio['id']; ?>">Descripción de la imagen</label>
                                        <input id="texto-<?php echo (int) $anuncio['id']; ?>" type="text" name="texto_alternativo" maxlength="180" value="<?php echo escaparCarrusel($anuncio['texto_alternativo']); ?>" required>
                                    </div>
                                    <div class="campo">
                                        <label for="orden-<?php echo (int) $anuncio['id']; ?>">Orden</label>
                                        <input id="orden-<?php echo (int) $anuncio['id']; ?>" type="number" name="orden_visual" min="1" max="9999" value="<?php echo (int) $anuncio['orden_visual']; ?>" required>
                                    </div>
                                    <div class="campo">
                                        <label for="imagen-<?php echo (int) $anuncio['id']; ?>-archivo">Reemplazar imagen (opcional)</label>
                                        <input id="imagen-<?php echo (int) $anuncio['id']; ?>-archivo" type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
                                    </div>
                                    <p class="ayuda">Si no eliges un archivo, se conserva la imagen actual.</p>
                                    <div class="acciones">
                                        <button class="boton" type="submit">Guardar cambios</button>
                                    </div>
                                </form>
                                <form method="post" onsubmit="return confirm('¿Eliminar este anuncio del carrusel?');">
                                    <input type="hidden" name="token" value="<?php echo escaparCarrusel($tokenCarrusel); ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo (int) $anuncio['id']; ?>">
                                    <div class="acciones">
                                        <button class="boton eliminar" type="submit">Eliminar anuncio</button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php require __DIR__ . '/../../vista/footer.php'; ?>
</body>
</html>
