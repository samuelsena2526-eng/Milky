<?php
// Funciones de sesión y autenticación utilizadas por las páginas PHP.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Escapa texto antes de imprimirlo en HTML.
function e($texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Comprueba si hay un usuario autenticado.
function estaLogueado(): bool {
    return isset($_SESSION['usuario']);
}

// Comprueba si el usuario autenticado es administrador.
function esAdmin(): bool {
    $usuario = $_SESSION['usuario'] ?? null;

    return (is_array($usuario) && strtolower((string)($usuario['rol'] ?? '')) === 'admin')
        || strtolower((string)($_SESSION['rol'] ?? '')) === 'admin';
}

// Obliga a que haya una sesión iniciada.
function requerirLogin(): void {
    if (!estaLogueado()) {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $projectPath = preg_replace('~/controlador(?:/.*)?$~', '', dirname($scriptName));
        header('Location: ' . rtrim($projectPath, '/') . '/login.php');
        exit;
    }
}

// Obliga a que el usuario sea administrador.
function requerirAdmin(): void {
    if (!esAdmin()) {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $projectPath = preg_replace('~/controlador/susana(?:/admin)?/[^/]+$~', '', $scriptName);
        header('Location: ' . rtrim($projectPath, '/') . '/InicioS.php');
        exit;
    }
}