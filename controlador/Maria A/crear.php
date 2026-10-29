<?php

require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/pregunta.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../conocenos.php#preguntas");
    exit;
}

$nombre = trim((string)($_POST["nombre"] ?? ""));
$pregunta = trim((string)($_POST["pregunta"] ?? ""));

if ($nombre === "" || $pregunta === "") {
    header("Location: ../../conocenos.php?mensaje=campos#preguntas");
    exit;
}

$objeto = new Pregunta();
$usuarioId = obtenerUsuarioSesionId();
if (!$objeto->crear($nombre, $pregunta, $usuarioId)) {
    header("Location: ../../conocenos.php?mensaje=error#preguntas");
    exit;
}

header("Location: ../../conocenos.php?mensaje=gracias#preguntas");
exit;