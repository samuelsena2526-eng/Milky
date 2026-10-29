<?php
// Controlador CRUD de productos.
// Operaciones: crear, actualizar y eliminar.
// Las consultas se hacen con PDO y parámetros preparados.

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/auth.php";

requerirAdmin();

$directorioImagenes = __DIR__ . '/../vista/img/productos';
$tiposImagen = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
$guardarImagen = static function (?array $archivo) use ($directorioImagenes, $tiposImagen): ?string {
    if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($archivo['size'] ?? 0) > 5 * 1024 * 1024) {
        return null;
    }

    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
    if (!isset($tiposImagen[$tipo])) {
        return null;
    }

    if (!is_dir($directorioImagenes) && !mkdir($directorioImagenes, 0755, true) && !is_dir($directorioImagenes)) {
        return null;
    }

    $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $tiposImagen[$tipo];
    if (!move_uploaded_file($archivo['tmp_name'], $directorioImagenes . '/' . $nombreArchivo)) {
        return null;
    }

    return 'productos/' . $nombreArchivo;
};
$eliminarImagenSubidaSiNoUsada = static function (string $imagen) use ($pdo, $directorioImagenes): void {
    if (strpos($imagen, 'productos/') !== 0) {
        return;
    }

    $consulta = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE imagen = ?');
    $consulta->execute([$imagen]);
    if ((int)$consulta->fetchColumn() === 0) {
        $nombreArchivo = basename($imagen);
        $ruta = $directorioImagenes . '/' . $nombreArchivo;
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
};
$rutaRetorno = ($_POST['origen'] ?? '') === 'catalogo'
    ? '../productosSU.php'
    : '../admin/productos.php';

$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '' || $precio <= 0 || $descripcion === '') {
        header("Location: $rutaRetorno?error=datos");
        exit;
    }

    $imagen = $guardarImagen($_FILES['imagen'] ?? null);
    if ($imagen === null) {
        header("Location: $rutaRetorno?error=imagen");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO productos (nombre, precio, descripcion, imagen)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$nombre, $precio, $descripcion, $imagen]);

    header("Location: $rutaRetorno?mensaje=creado");
    exit;
}

if ($accion === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $consultaImagen = $pdo->prepare('SELECT imagen FROM productos WHERE id = ?');
    $consultaImagen->execute([$id]);
    $imagenAnterior = $consultaImagen->fetchColumn();

    if ($id <= 0 || $nombre === '' || $precio <= 0 || $descripcion === '' || $imagenAnterior === false) {
        header("Location: ../admin/productos.php?editar=$id&error=datos");
        exit;
    }

    $imagen = $imagenAnterior;
    if (($_FILES['imagen']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $imagen = $guardarImagen($_FILES['imagen'] ?? null);
        if ($imagen === null) {
            header("Location: ../admin/productos.php?editar=$id&error=imagen");
            exit;
        }
    }

    $stmt = $pdo->prepare(
        "UPDATE productos
         SET nombre = ?, precio = ?, descripcion = ?, imagen = ?
         WHERE id = ?"
    );
    $stmt->execute([$nombre, $precio, $descripcion, $imagen, $id]);
    if ($imagen !== $imagenAnterior) {
        $eliminarImagenSubidaSiNoUsada($imagenAnterior);
    }

    header("Location: ../admin/productos.php?mensaje=actualizado");
    exit;
}

if ($accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);

    if ($id > 0) {
        $consultaImagen = $pdo->prepare('SELECT imagen FROM productos WHERE id = ?');
        $consultaImagen->execute([$id]);
        $imagen = $consultaImagen->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        try {
            $stmt->execute([$id]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                header('Location: ../admin/productos.php?error=en_carrito');
                exit;
            }

            throw $exception;
        }
        if ($imagen !== false) {
            $eliminarImagenSubidaSiNoUsada($imagen);
        }
    }

    header("Location: ../admin/productos.php?mensaje=eliminado");
    exit;
}

header("Location: ../admin/productos.php");
exit;
