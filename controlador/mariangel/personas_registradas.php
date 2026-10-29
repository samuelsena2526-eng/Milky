<?php
session_start();

if (($_SESSION['rol'] ?? '') !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

include(__DIR__ . '/../conexion/conexion.php');

$consulta = $conexion->query(
    'SELECT id, nombre, correo, rol FROM usuarios ORDER BY id DESC'
);
$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuentas registradas</title>
    <link rel="stylesheet" href="../../vista/css/inidifer.css?v=<?php echo filemtime(dirname(__DIR__, 2) . '/vista/css/inidifer.css'); ?>">
</head>
<body>
    <main class="contenedor cuentas">
        <h1>Cuentas registradas</h1>
        <div class="tabla-scroll">
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="acciones-usuario">
                           <input type="button" value="Editar" onclick="window.location.href='editar.php?id=<?php echo $usuario['id']; ?>'">
                            <form method="post" action="eliminar.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <button type="submit" onclick="return confirm('¿Deseas eliminar este usuario?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <p><a href="../../index.php">Volver al inicio</a></p>
    </main>
</body>
</html>