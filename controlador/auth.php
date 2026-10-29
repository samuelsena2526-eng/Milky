<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/susana/autenticacion.php";

if (isset($conexion) && !isset($pdo)) {
    $pdo = $conexion;
}

function obtenerUsuarioSesionId(): ?int {
    global $pdo;

    $usuario = $_SESSION['usuario'] ?? null;
    if (is_array($usuario) && filter_var($usuario['id'] ?? null, FILTER_VALIDATE_INT) !== false) {
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE id = ? LIMIT 1');
        $consulta->execute([(int)$usuario['id']]);
        $id = $consulta->fetchColumn();
        if ($id !== false) {
            return (int)$id;
        }
    }

    $identificador = is_string($usuario)
        ? $usuario
        : (string)($usuario['correo'] ?? '');
    if ($identificador !== '') {
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE correo = ? LIMIT 1');
        $consulta->execute([$identificador]);
        $id = $consulta->fetchColumn();
        if ($id !== false) {
            return (int)$id;
        }
    }

    return null;
}
