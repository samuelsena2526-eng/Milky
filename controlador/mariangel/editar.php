<?php
session_start();

if (($_SESSION['rol'] ?? '') !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once __DIR__ . '/../conexion/conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = '';

if (!$id) {
    $error = 'El usuario indicado no es válido.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = $_POST['rol'] ?? 'Usuario';
    $contrasena = $_POST['contraseña'] ?? '';

    if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Escribe un nombre y un correo válido.';
    } elseif (!in_array($rol, ['Admin', 'Usuario'], true)) {
        $error = 'El rol seleccionado no es válido.';
    } else {
        $campos = 'nombre = :nombre, correo = :correo, rol = :rol';
        $parametros = [
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':rol' => $rol,
            ':id' => $id,
        ];

        if ($contrasena !== '') {
            $campos .= ', `contraseña` = :contrasena';
            $parametros[':contrasena'] = $contrasena;
        }

        $consulta = $conexion->prepare("UPDATE usuarios SET $campos WHERE id = :id");
        $consulta->execute($parametros);
        header('Location: personas_registradas.php?actualizado=1');
        exit;
    }
}

if (!$error) {
    $consulta = $conexion->prepare('SELECT id, nombre, correo, rol FROM usuarios WHERE id = :id');
    $consulta->execute([':id' => $id]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $error = 'No se encontró el usuario.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="../../vista/css/inidifer.css">
</head>
<body>
    <main class="contenedor">
        <h1>Editar usuario</h1>

        <?php if ($error): ?>
            <p role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <form method="post" action="editar.php?id=<?php echo $usuario['id']; ?>">
                <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <input type="email" name="correo" placeholder="Correo" value="<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <select name="rol" required>
                    <option value="Usuario" <?php echo $usuario['rol'] === 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                    <option value="Admin" <?php echo $usuario['rol'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <input type="password" name="contraseña" placeholder="Nueva contraseña (opcional)">
                <button type="submit">Guardar cambios</button>
            </form>
        <?php endif; ?>

        <p><a href="personas_registradas.php">Volver a usuarios</a></p>
    </main>
</body>
</html>
 