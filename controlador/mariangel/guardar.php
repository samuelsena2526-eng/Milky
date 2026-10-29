<?php
session_start();
require_once __DIR__ . '/../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../registro.php');
    exit;
}

$nombre = trim((string)($_POST['nombre'] ?? ''));
$correo = strtolower(trim((string)($_POST['correo'] ?? '')));
$contrasena = $_POST['contraseña'] ?? '';

if ($nombre === '' || $correo === '' || !is_string($contrasena) || $contrasena === '') {
    header('Location: ../../registro.php?error=campos');
    exit;
}
$longitudNombre = function_exists('mb_strlen') ? mb_strlen($nombre, 'UTF-8') : strlen($nombre);
if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $longitudNombre > 120 || strlen($correo) > 150) {
    header('Location: ../../registro.php?error=datos');
    exit;
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);
try {
    $stmt = $conexion->prepare(
        "INSERT INTO usuarios (nombre, correo, `contraseña`, rol) VALUES (:nombre, :correo, :contrasena, 'cliente')"
    );
    $stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'contrasena' => $hash]);
    header('Location: ../../InicioS.php?registro=ok');
    exit;
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? null) === 1062) {
        header('Location: ../../registro.php?error=duplicado');
        exit;
    }

    error_log('Error al registrar usuario: ' . $e->getMessage());
    header('Location: ../../registro.php?error=registro');
    exit;
}
