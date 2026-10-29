<?php
require_once __DIR__ . "/conexion/conexion.php";

if (isset($conexion) && !isset($pdo)) {
    $pdo = $conexion;
}
