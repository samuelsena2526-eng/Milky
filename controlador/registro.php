<?php
require_once __DIR__ . "/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../registro.php");
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$contraseña = $_POST['contraseña'] ?? '';

if ($nombre === '' || $contraseña === '') {
    header("Location: ../registro.php?error=campos");
    exit;
}

$hash = password_hash($contraseña, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, contraseña, rol) VALUES (?, ?, 'cliente')");
    $stmt->execute([$nombre, $hash]);

    header("Location: ../login.php?registro=ok");
    exit;
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? null) == 1062) {
        header("Location: ../registro.php?error=duplicado");
        exit;
    }

    die("No se pudo registrar el usuario.");
}
