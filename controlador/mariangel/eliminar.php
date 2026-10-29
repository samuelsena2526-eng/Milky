<?php
session_start();

if (($_SESSION['rol'] ?? '') !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: personas_registradas.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$correoSesion = $_SESSION['usuario'] ?? '';

if (!$id) {
    header('Location: personas_registradas.php?error=usuario_invalido');
    exit;
}

require_once __DIR__ . '/../conexion/conexion.php';

$consulta = $conexion->prepare('SELECT correo FROM usuarios WHERE id = :id');
$consulta->execute([':id' => $id]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['correo'] === $correoSesion) {
    header('Location: personas_registradas.php?error=no_se_puede_eliminar');
    exit;
}

$consulta = $conexion->prepare('DELETE FROM usuarios WHERE id = :id');
$consulta->execute([':id' => $id]);

header('Location: personas_registradas.php?eliminado=1');
exit;
