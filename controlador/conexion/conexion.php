<?php

// Datos de la base de datos
$servidor = "localhost"; // Servidor de la base de datos
$usuario = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$bd = "milky"; // Nombre de la base de datos
$port = 3306; // Puerto de la base de datos

try {
    $dsn = "mysql:host=$servidor;port=$port;dbname=$bd;charset=utf8mb4";
    $conexion = new PDO($dsn, $usuario, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Inicializa las tablas mínimas que usa la app para que no falle el flujo principal.
    $conexion->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            rol VARCHAR(20) NOT NULL DEFAULT 'cliente',
            nombre VARCHAR(120) NULL,
            correo VARCHAR(150) NULL,
            contraseña VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_usuarios_correo (correo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $columnasUsuarios = $conexion->query("SHOW COLUMNS FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('rol', $columnasUsuarios, true)) {
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'cliente' AFTER id");
    }
    if (!in_array('nombre', $columnasUsuarios, true)) {
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN nombre VARCHAR(120) NULL AFTER rol");
    }
    if (!in_array('correo', $columnasUsuarios, true)) {
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN correo VARCHAR(150) NULL AFTER nombre");
    }
    if (!in_array('contraseña', $columnasUsuarios, true)) {
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN contraseña VARCHAR(255) NULL AFTER correo");
    }

    // Los hashes de password_hash() superan los 50 caracteres del esquema antiguo.
    // Amplía instalaciones ya creadas, además de usar VARCHAR(255) en tablas nuevas.
    $columnaContrasena = $conexion->query("SHOW COLUMNS FROM usuarios LIKE 'contraseña'")->fetch(PDO::FETCH_ASSOC);
    if (
        $columnaContrasena
        && preg_match('/^varchar\\((\\d+)\\)$/i', $columnaContrasena['Type'], $longitudContrasena)
        && (int)$longitudContrasena[1] < 255
    ) {
        $nullable = $columnaContrasena['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $conexion->exec("ALTER TABLE usuarios MODIFY COLUMN `contraseña` VARCHAR(255) $nullable");
    }

    if (in_array('usuario', $columnasUsuarios, true)) {
        $conexion->exec("UPDATE usuarios SET nombre = usuario WHERE (nombre IS NULL OR TRIM(nombre) = '') AND usuario IS NOT NULL");

        $indicesUsuarios = $conexion->query("SHOW INDEX FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
        $indicesPorEliminar = [];
        foreach ($indicesUsuarios as $indice) {
            if ($indice['Column_name'] === 'usuario' && $indice['Key_name'] !== 'PRIMARY') {
                $indicesPorEliminar[$indice['Key_name']] = true;
            }
        }
        foreach (array_keys($indicesPorEliminar) as $nombreIndice) {
            $nombreIndice = str_replace('`', '``', $nombreIndice);
            $conexion->exec("ALTER TABLE usuarios DROP INDEX `$nombreIndice`");
        }
        $conexion->exec("ALTER TABLE usuarios DROP COLUMN usuario");
    }
    if (in_array('password', $columnasUsuarios, true)) {
        $conexion->exec("UPDATE usuarios SET contraseña = password WHERE (contraseña IS NULL OR TRIM(contraseña) = '') AND password IS NOT NULL");
        $conexion->exec("ALTER TABLE usuarios DROP COLUMN password");
    }
    $conexion->exec("UPDATE usuarios SET rol = 'cliente' WHERE rol IS NULL OR TRIM(rol) = ''");

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS productos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(150) NOT NULL,
            precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            descripcion TEXT NOT NULL,
            imagen VARCHAR(255) NOT NULL DEFAULT 'producto.jpg',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $usuarioIdSchema = $conexion->query("SHOW COLUMNS FROM usuarios LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
    if (
        stripos($usuarioIdSchema['Type'], 'unsigned') === false
        || stripos($usuarioIdSchema['Extra'], 'auto_increment') === false
    ) {
        $conexion->exec("ALTER TABLE usuarios MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    }

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS carritos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            usuario_id INT UNSIGNED NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_carritos_usuario (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS carrito_items (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            carrito_id INT UNSIGNED NOT NULL,
            producto_id INT UNSIGNED NOT NULL,
            cantidad INT UNSIGNED NOT NULL DEFAULT 1,
            agregado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_carrito_items_producto (carrito_id, producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $conexion->exec("
        INSERT INTO productos (nombre, precio, descripcion, imagen)
        SELECT * FROM (
            SELECT 'Leche Fresca' AS nombre, 6500.00 AS precio, 'Leche entera, rica en calcio y sabor natural.' AS descripcion, 'publiciadad/p.jpeg' AS imagen
            UNION ALL SELECT 'Yogur Natural', 5800.00, 'Yogur cremoso con fermentos naturales.', 'publiciadad/2.jpg'
            UNION ALL SELECT 'Queso Campesino', 6800.00, 'Queso artesanal con textura suave y sabor tradicional.', 'publiciadad/5.jpeg'
            UNION ALL SELECT 'Mantequilla', 9400.00, 'Mantequilla de alta calidad para cocinar y untar.', 'publiciadad/1.jpeg'
            UNION ALL SELECT 'Leche sabor fresa macaron', 9900.00, 'Leche semidescremada con sabor a fresa y macaron, en presentación de 250 ml.', 'publiciadad/3.jpg'
            UNION ALL SELECT 'Yogur con cereal', 4200.00, 'Yogur cremoso acompañado de cereal frutal y hojuelas crujientes.', 'publiciadad/4.jpeg'
            UNION ALL SELECT 'Leche en polvo clásica', 24900.00, 'Leche entera en polvo fortificada con calcio, zinc, proteína y vitaminas.', 'publiciadad/6.jpeg'
        ) AS nuevas
        WHERE NOT EXISTS (SELECT 1 FROM productos)
    ");

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS preguntas (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            pregunta TEXT NOT NULL,
            respuesta TEXT NULL,
            usuario_id INT UNSIGNED NULL,
            respondido_por INT UNSIGNED NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS carrusel_imagenes (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            ruta VARCHAR(255) NOT NULL,
            texto_alternativo VARCHAR(180) NOT NULL,
            orden_visual INT UNSIGNED NOT NULL DEFAULT 1,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            creado_por INT UNSIGNED NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $asegurarColumnaOpcional = static function (string $tabla, string $columna, string $definicion) use ($conexion): void {
        $consulta = $conexion->prepare(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
        );
        $consulta->execute([$tabla, $columna]);
        if ((int)$consulta->fetchColumn() === 0) {
            $conexion->exec("ALTER TABLE `$tabla` ADD COLUMN `$columna` $definicion");
        }
    };

    $asegurarColumnaOpcional('preguntas', 'respuesta', 'TEXT NULL');
    $asegurarColumnaOpcional('preguntas', 'usuario_id', 'INT UNSIGNED NULL');
    $asegurarColumnaOpcional('preguntas', 'respondido_por', 'INT UNSIGNED NULL');
    $asegurarColumnaOpcional('carrusel_imagenes', 'creado_por', 'INT UNSIGNED NULL');

    $estadisticasPreguntas = $conexion->query(
        "SELECT COUNT(*) AS total, COUNT(DISTINCT id) AS distintos FROM preguntas"
    )->fetch(PDO::FETCH_ASSOC);
    $columnaIdTemporal = $conexion->query(
        "SHOW COLUMNS FROM preguntas LIKE 'id_milky_migracion'"
    )->fetch(PDO::FETCH_ASSOC);
    if (
        $columnaIdTemporal
        || (int)$estadisticasPreguntas['total'] !== (int)$estadisticasPreguntas['distintos']
    ) {
        if (!$columnaIdTemporal) {
            $conexion->exec(
                "ALTER TABLE preguntas ADD COLUMN id_milky_migracion INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE"
            );
        }
        $conexion->exec("UPDATE preguntas SET id = id_milky_migracion");
        $conexion->exec("ALTER TABLE preguntas DROP COLUMN id_milky_migracion");
    }

    $indicesPreguntas = $conexion->query("SHOW INDEX FROM preguntas")->fetchAll(PDO::FETCH_ASSOC);
    $preguntaIdUnique = false;
    $preguntaPrimaryExists = false;
    foreach ($indicesPreguntas as $indice) {
        $preguntaPrimaryExists = $preguntaPrimaryExists || $indice['Key_name'] === 'PRIMARY';
        if ($indice['Column_name'] === 'id' && (int)$indice['Non_unique'] === 0) {
            $preguntaIdUnique = true;
        }
    }
    if (!$preguntaIdUnique) {
        if (!$preguntaPrimaryExists) {
            $conexion->exec("ALTER TABLE preguntas ADD PRIMARY KEY (id)");
        } else {
            $conexion->exec("ALTER TABLE preguntas ADD UNIQUE KEY uq_preguntas_id (id)");
        }
    }
    $preguntasConIdCero = (int)$conexion->query("SELECT COUNT(*) FROM preguntas WHERE id = 0")->fetchColumn();
    if ($preguntasConIdCero > 0) {
        $siguienteIdPregunta = (int)$conexion->query("SELECT COALESCE(MAX(id), 0) + 1 FROM preguntas")->fetchColumn();
        $conexion->exec("UPDATE preguntas SET id = $siguienteIdPregunta WHERE id = 0");
    }
    $preguntaIdSchema = $conexion->query("SHOW COLUMNS FROM preguntas LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
    if (stripos($preguntaIdSchema['Extra'], 'auto_increment') === false) {
        $conexion->exec("ALTER TABLE preguntas MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    }

    $estadisticas = $conexion->query(
        "SELECT
            (SELECT COUNT(*) FROM carritos) AS carritos_total,
            (SELECT COUNT(DISTINCT id) FROM carritos) AS carritos_ids,
            (SELECT COUNT(*) FROM carrito_items) AS items_total,
            (SELECT COUNT(DISTINCT id) FROM carrito_items) AS items_ids"
    )->fetch(PDO::FETCH_ASSOC);
    if (
        (int)$estadisticas['carritos_total'] !== (int)$estadisticas['carritos_ids']
        || (int)$estadisticas['items_total'] !== (int)$estadisticas['items_ids']
    ) {
        throw new RuntimeException('No se pueden relacionar los carritos: hay identificadores repetidos.');
    }

    $indicesCarritos = $conexion->query("SHOW INDEX FROM carritos")->fetchAll(PDO::FETCH_ASSOC);
    $carritoIdUnique = false;
    $carritoPrimaryExists = false;
    $usuarioCarritoIndexed = false;
    foreach ($indicesCarritos as $indice) {
        $carritoPrimaryExists = $carritoPrimaryExists || $indice['Key_name'] === 'PRIMARY';
        if ($indice['Column_name'] === 'id') {
            $carritoIdUnique = $carritoIdUnique || (int)$indice['Non_unique'] === 0;
        }
        if ($indice['Column_name'] === 'usuario_id' && (int)$indice['Non_unique'] === 0) {
            $usuarioCarritoIndexed = true;
        }
    }
    if (!$carritoIdUnique) {
        if (!$carritoPrimaryExists) {
            $conexion->exec("ALTER TABLE carritos ADD PRIMARY KEY (id)");
        } else {
            $conexion->exec("ALTER TABLE carritos ADD UNIQUE KEY uq_carritos_id (id)");
        }
    }
    $carritosConIdCero = (int)$conexion->query("SELECT COUNT(*) FROM carritos WHERE id = 0")->fetchColumn();
    if ($carritosConIdCero > 0) {
        $siguienteIdCarrito = (int)$conexion->query("SELECT COALESCE(MAX(id), 0) + 1 FROM carritos")->fetchColumn();
        $consultaCarritosItems = $conexion->prepare(
            "UPDATE carrito_items SET carrito_id = ? WHERE carrito_id = 0"
        );
        $consultaCarritosItems->execute([$siguienteIdCarrito]);
        $consultaCarritoSinId = $conexion->prepare("UPDATE carritos SET id = ? WHERE id = 0");
        $consultaCarritoSinId->execute([$siguienteIdCarrito]);
    }
    $carritoIdSchema = $conexion->query("SHOW COLUMNS FROM carritos LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
    if (stripos($carritoIdSchema['Extra'], 'auto_increment') === false) {
        $conexion->exec("ALTER TABLE carritos MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    $usuarioCarritoSchema = $conexion->query("SHOW COLUMNS FROM carritos LIKE 'usuario_id'")->fetch(PDO::FETCH_ASSOC);
    if (
        stripos($usuarioCarritoSchema['Type'], 'unsigned') === false
        || $usuarioCarritoSchema['Null'] !== 'YES'
    ) {
        $conexion->exec("ALTER TABLE carritos MODIFY usuario_id INT UNSIGNED NULL");
    }
    if (!$usuarioCarritoIndexed) {
        $conexion->exec("ALTER TABLE carritos ADD UNIQUE KEY uq_carritos_usuario (usuario_id)");
    }
    $conexion->exec(
        "UPDATE carritos c
         LEFT JOIN usuarios u ON u.id = c.usuario_id
         SET c.usuario_id = NULL
         WHERE c.usuario_id IS NOT NULL AND u.id IS NULL"
    );

    $indicesItems = $conexion->query("SHOW INDEX FROM carrito_items")->fetchAll(PDO::FETCH_ASSOC);
    $itemIdUnique = false;
    $itemPrimaryExists = false;
    $itemPairIndexed = false;
    foreach ($indicesItems as $indice) {
        $itemPrimaryExists = $itemPrimaryExists || $indice['Key_name'] === 'PRIMARY';
        if ($indice['Column_name'] === 'id') {
            $itemIdUnique = $itemIdUnique || (int)$indice['Non_unique'] === 0;
        }
        if ($indice['Key_name'] === 'uq_carrito_items_producto') {
            $itemPairIndexed = true;
        }
    }
    if (!$itemIdUnique) {
        if (!$itemPrimaryExists) {
            $conexion->exec("ALTER TABLE carrito_items ADD PRIMARY KEY (id)");
        } else {
            $conexion->exec("ALTER TABLE carrito_items ADD UNIQUE KEY uq_carrito_items_id (id)");
        }
    }
    $itemsConIdCero = (int)$conexion->query("SELECT COUNT(*) FROM carrito_items WHERE id = 0")->fetchColumn();
    if ($itemsConIdCero > 0) {
        $siguienteIdItem = (int)$conexion->query("SELECT COALESCE(MAX(id), 0) + 1 FROM carrito_items")->fetchColumn();
        $consultaItemSinId = $conexion->prepare("UPDATE carrito_items SET id = ? WHERE id = 0");
        $consultaItemSinId->execute([$siguienteIdItem]);
    }
    $itemIdSchema = $conexion->query("SHOW COLUMNS FROM carrito_items LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
    if (stripos($itemIdSchema['Extra'], 'auto_increment') === false) {
        $conexion->exec("ALTER TABLE carrito_items MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    foreach (['carrito_id', 'producto_id'] as $columnaRelacion) {
        $columnaRelacionSchema = $conexion->query(
            "SHOW COLUMNS FROM carrito_items LIKE '$columnaRelacion'"
        )->fetch(PDO::FETCH_ASSOC);
        if (stripos($columnaRelacionSchema['Type'], 'unsigned') === false) {
            $conexion->exec(
                "ALTER TABLE carrito_items MODIFY `$columnaRelacion` INT UNSIGNED NOT NULL"
            );
        }
    }
    if (!$itemPairIndexed) {
        $repetidos = $conexion->query(
            "SELECT COUNT(*) FROM (
                SELECT carrito_id, producto_id FROM carrito_items
                GROUP BY carrito_id, producto_id HAVING COUNT(*) > 1
            ) repetidos"
        )->fetchColumn();
        if ((int)$repetidos !== 0) {
            throw new RuntimeException('No se puede completar la relación: hay productos repetidos en un carrito.');
        }
        $conexion->exec(
            "ALTER TABLE carrito_items
             ADD UNIQUE KEY uq_carrito_items_producto (carrito_id, producto_id)"
        );
    }

    foreach (['usuario_id', 'respondido_por'] as $columnaRelacion) {
        $columnaRelacionSchema = $conexion->query(
            "SHOW COLUMNS FROM preguntas LIKE '$columnaRelacion'"
        )->fetch(PDO::FETCH_ASSOC);
        if (
            stripos($columnaRelacionSchema['Type'], 'unsigned') === false
            || $columnaRelacionSchema['Null'] !== 'YES'
        ) {
            $conexion->exec(
                "ALTER TABLE preguntas MODIFY `$columnaRelacion` INT UNSIGNED NULL"
            );
        }
    }
    $creadoPorSchema = $conexion->query("SHOW COLUMNS FROM carrusel_imagenes LIKE 'creado_por'")->fetch(PDO::FETCH_ASSOC);
    if (
        stripos($creadoPorSchema['Type'], 'unsigned') === false
        || $creadoPorSchema['Null'] !== 'YES'
    ) {
        $conexion->exec("ALTER TABLE carrusel_imagenes MODIFY creado_por INT UNSIGNED NULL");
    }

    $agregarRelacion = static function (
        string $nombre,
        string $tabla,
        string $columna,
        string $tablaReferida,
        string $columnaReferida,
        string $alEliminar
    ) use ($conexion): void {
        $consulta = $conexion->prepare(
            "SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?"
        );
        $consulta->execute([$tabla, $nombre]);
        if ((int)$consulta->fetchColumn() !== 0) {
            return;
        }

        $conexion->exec(
            "ALTER TABLE `$tabla`
             ADD CONSTRAINT `$nombre`
             FOREIGN KEY (`$columna`) REFERENCES `$tablaReferida` (`$columnaReferida`)
             ON UPDATE CASCADE ON DELETE $alEliminar"
        );
    };

    $agregarRelacion('fk_carritos_usuario', 'carritos', 'usuario_id', 'usuarios', 'id', 'SET NULL');
    $agregarRelacion('fk_carrito_items_carrito', 'carrito_items', 'carrito_id', 'carritos', 'id', 'CASCADE');
    $agregarRelacion('fk_carrito_items_producto', 'carrito_items', 'producto_id', 'productos', 'id', 'RESTRICT');
    $agregarRelacion('fk_preguntas_usuario', 'preguntas', 'usuario_id', 'usuarios', 'id', 'SET NULL');
    $agregarRelacion('fk_preguntas_respondido_por', 'preguntas', 'respondido_por', 'usuarios', 'id', 'SET NULL');
    $agregarRelacion('fk_carrusel_creado_por', 'carrusel_imagenes', 'creado_por', 'usuarios', 'id', 'SET NULL');

    $conexion->exec("
        INSERT INTO carrusel_imagenes (ruta, texto_alternativo, orden_visual)
        SELECT * FROM (
            SELECT 'vista/img/publiciadad/1.jpeg' AS ruta, 'Anuncio Milky de queso crema' AS texto_alternativo, 1 AS orden_visual
            UNION ALL SELECT 'vista/img/publiciadad/2.jpg', 'Anuncio de productos Milky', 2
            UNION ALL SELECT 'vista/img/publiciadad/3.jpg', 'Anuncio de producto natural Milky', 3
            UNION ALL SELECT 'vista/img/publiciadad/4.jpeg', 'Anuncio de mantequilla Milky', 4
            UNION ALL SELECT 'vista/img/publiciadad/5.jpeg', 'Anuncio de nuevo sabor Milky', 5
            UNION ALL SELECT 'vista/img/publiciadad/6.jpeg', 'Anuncio de productos Milky', 6
        ) AS imagenes
        WHERE NOT EXISTS (SELECT 1 FROM carrusel_imagenes)
    ");

    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $conexion->exec("
        INSERT INTO usuarios (nombre, contraseña, rol)
        SELECT 'admin', '$adminHash', 'admin'
        WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre = 'admin')
    ");

} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
}

?>
