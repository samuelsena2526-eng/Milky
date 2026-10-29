<?php
require_once __DIR__ . "/../auth.php";

if (!esAdmin()) {
    header("Location: ../../InicioS.php");
    exit;
}

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
    || !isset($_SESSION["token_preguntas"])
    || !hash_equals($_SESSION["token_preguntas"], (string)($_POST["token"] ?? ""))
) {
    header("Location: ../../conocenos.php?mensaje=error#preguntas");
    exit;
}

$id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
$respuesta = trim((string)($_POST["respuesta"] ?? ""));
$usuarioId = obtenerUsuarioSesionId();

if ($id === false || $respuesta === "" || $usuarioId === null) {
    header("Location: ../../conocenos.php?mensaje=error#preguntas");
    exit;
}

require_once __DIR__ . "/pregunta.php";
$objeto = new Pregunta();
if (!$objeto->responder($id, $respuesta, $usuarioId)) {
    header("Location: ../../conocenos.php?mensaje=error#preguntas");
    exit;
}

header("Location: ../../conocenos.php?mensaje=respondido#preguntas");
exit;
