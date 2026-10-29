<?php
// Controlador CRUD de productos.
// Operaciones: crear, actualizar y eliminar.
// Las consultas se hacen con PDO y parámetros preparados.

require_once __DIR__ . "/../conexion/conexion.php";
require_once __DIR__ . "/autenticacion.php";

$pdo = $conexion;

requerirAdmin();

$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if ($nombre === '' || $precio <= 0 || $imagen === '') {
        header("Location: ../admin/productos.php?error=datos");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO productos (nombre, precio, descripcion, imagen)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$nombre, $precio, $descripcion, $imagen]);

    header("Location: ../admin/productos.php?mensaje=creado");
    exit;
}

if ($accion === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if ($id <= 0 || $nombre === '' || $precio <= 0 || $imagen === '') {
        header("Location: ../admin/productos.php?error=datos");
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE productos
         SET nombre = ?, precio = ?, descripcion = ?, imagen = ?
         WHERE id = ?"
    );
    $stmt->execute([$nombre, $precio, $descripcion, $imagen, $id]);

    header("Location: ../admin/productos.php?mensaje=actualizado");
    exit;
}

if ($accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: ../admin/productos.php?mensaje=eliminado");
    exit;
}

header("Location: ../admin/productos.php");
exit;