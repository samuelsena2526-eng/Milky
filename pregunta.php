<?php

// Incluye el archivo Pregunta.php desde el directorio controlador
require_once __DIR__ . "/controlador/Maria A/pregunta.php";

// Crea una instancia de la clase Pregunta
$objeto = new Pregunta();

// Llama al método leer() para obtener todas las preguntas
$preguntas = $objeto->leer();
$usuarioPreguntas = $_SESSION["usuario"] ?? null;
$esAdminPreguntas = strtolower((string)($_SESSION["rol"] ?? "")) === "admin"
    || (is_array($usuarioPreguntas) && strtolower((string)($usuarioPreguntas["rol"] ?? "")) === "admin");
$tokenPreguntas = $esAdminPreguntas
    ? ($_SESSION["token_preguntas"] ??= bin2hex(random_bytes(32)))
    : "";

// Inicializa la variable $editar como null
$editar = null;

// Verifica si se ha enviado el parámetro "editar" en la URL
if ($esAdminPreguntas && isset($_GET["editar"])) {
    // Obtiene el valor del parámetro "editar" de la URL
    $id = $_GET["editar"];
    // Llama al método buscar() para obtener los datos de la pregunta con el ID especificado
    $editar = $objeto->buscar($id);
}

?>

<style>

/* Estilo para el contenedor principal de preguntas */
.contenedor-preguntas {
    width: 90%;
    max-width: 650px;
    margin: 40px auto;
    font-family: Arial, sans-serif;
}

/* Estilo para la caja principal de preguntas */
.caja-pregunta {
    background: white;
    color: #333;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.25);
    text-align: center;
    margin-bottom: 35px;
    font-family: "Dancing Script", cursive;
}

/* Estilo para el título dentro de la caja de preguntas */
.caja-pregunta h2 {
    color: #333;
    font-size: 28px;
    margin-bottom: 10px;
}

/* Estilo para el texto dentro de la caja de preguntas */
.caja-pregunta p {
    color: #666;
    font-size: 16px;
}

.aviso-pregunta {
    padding: 14px 18px;
    margin-bottom: 18px;
    border-radius: 10px;
    color: #315b28;
    background: #e8f0d5;
    font-family: Arial, sans-serif;
}

/* Estilo para los campos de entrada de texto */
.caja-pregunta input {
    width: 100%;
    padding: 14px;
    margin: 10px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 16px;
}

/* Estilo para el área de texto */
.caja-pregunta textarea {
    width: 100%;
    height: 120px;
    padding: 14px;
    margin: 10px 0 15px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 16px;
    resize: none;
}

/* Estilo para el botón de enviar */
.btn-enviar {
    width: 100%;
    padding: 14px;
    background: #8e44ad;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
}

/* Estilo para la lista de preguntas */
.lista-preguntas {
    color: white;
    text-align: center;
}

/* Estilo para el título de la lista de preguntas */
.lista-preguntas h2 {
    margin-bottom: 20px;
}

/* Estilo para cada tarjeta de pregunta */
.tarjeta-pregunta {
    background: white;
    color: #333;
    padding: 20px;
    margin: 15px 0;
    border-radius: 15px;
    text-align: left;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Estilo para el título dentro de cada tarjeta de pregunta */
.tarjeta-pregunta h3 {
    color: #8e44ad;
    margin-top: 0;
}

/* Estilo para el texto dentro de cada tarjeta de pregunta */
.tarjeta-pregunta p {
    color: #333;
}

.respuesta-pregunta {
    padding: 12px 16px;
    margin-top: 14px;
    border-left: 4px solid #7a8744;
    border-radius: 8px;
    background: #f1f5e8;
}

.form-respuesta {
    margin-top: 16px;
}

.form-respuesta textarea {
    width: 100%;
    min-height: 90px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 10px;
    font: inherit;
    resize: vertical;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Estilo para el contenedor de botones de acciones */
.acciones {
    margin-top: 15px;
}

/* Estilo para el botón de editar */
.btn-editar {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin-right: 5px;
    background: #8e44ad;
    color: white;
}

/* Estilo para el botón de eliminar */
.btn-eliminar {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin-right: 5px;
    background: #c0392b;
    color: white;
}

.acciones form {
    display: inline;
}

.acciones button.btn-eliminar {
    border: 0;
    font: inherit;
    cursor: pointer;
}

/* Estilo para el botón de guardar */
.btn-guardar {
    padding: 12px 18px;
    background: #8e44ad;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

/* Estilo para el botón de cancelar */
.btn-cancelar {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    background: #777;
    color: white;
}

</style>

<div class="contenedor-preguntas" id="preguntas">

    <?php if (($_GET["mensaje"] ?? "") === "gracias"): ?>
        <p class="aviso-pregunta" role="status">¡Gracias por compartir tu pregunta! La recibimos correctamente.</p>
    <?php elseif (($_GET["mensaje"] ?? "") === "actualizado"): ?>
        <p class="aviso-pregunta" role="status">Los cambios se guardaron correctamente.</p>
    <?php elseif (($_GET["mensaje"] ?? "") === "respondido"): ?>
        <p class="aviso-pregunta" role="status">La respuesta se guardó correctamente.</p>
    <?php elseif (($_GET["mensaje"] ?? "") === "eliminado"): ?>
        <p class="aviso-pregunta" role="status">La pregunta se eliminó correctamente.</p>
    <?php elseif (($_GET["mensaje"] ?? "") === "campos"): ?>
        <p class="aviso-pregunta" role="alert">Completa tu nombre y tu pregunta antes de enviarla.</p>
    <?php elseif (($_GET["mensaje"] ?? "") === "error"): ?>
        <p class="aviso-pregunta" role="alert">No pudimos completar la acción. Inténtalo nuevamente.</p>
    <?php endif; ?>

    <?php if ($editar): ?>

        <!-- Sección para editar una pregunta existente -->
        <div class="caja-pregunta">

            <h2>Editar pregunta</h2>

            <!-- Formulario para editar una pregunta -->
            <form action="controlador/Maria%20A/editar.php" method="POST">

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenPreguntas, ENT_QUOTES, "UTF-8"); ?>">
                <!-- Campo oculto para enviar el ID de la pregunta -->
                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editar["id"]; ?>"
                >

                <!-- Campo de entrada para el nombre -->
                <input
                    type="text"
                    name="nombre"
                    value="<?php echo htmlspecialchars($editar["nombre"]); ?>"
                    placeholder="Tu nombre"
                    required
                >

                <!-- Área de texto para la pregunta -->
                <textarea
                    name="pregunta"
                    placeholder="Escribe tu pregunta..."
                    required
                ><?php echo htmlspecialchars($editar["pregunta"]); ?></textarea>

                <!-- Botón para guardar los cambios -->
                <button
                    type="submit"
                    class="btn-guardar"
                >
                    GUARDAR CAMBIOS
                </button>

                <!-- Botón para cancelar la edición -->
                <a
                    href="conocenos.php"
                    class="btn-cancelar"
                >
                    CANCELAR
                </a>

            </form>

        </div>

    <?php else: ?>

        <!-- Sección para crear una nueva pregunta -->
        <div class="caja-pregunta">

            <h2>¿Tienes una pregunta?</h2>

            <p>
                Déjanos tu pregunta y te responderemos.
            </p>

            <!-- Formulario para crear una nueva pregunta -->
            <form
                action="controlador/Maria%20A/crear.php"
                method="POST"
            >

                <!-- Campo de entrada para el nombre -->
                <input
                    type="text"
                    name="nombre"
                    placeholder="Escribe tu nombre"
                    required
                >

                <!-- Área de texto para la pregunta -->
                <textarea
                    name="pregunta"
                    placeholder="Escribe aquí tu pregunta..."
                    required
                ></textarea>

                <!-- Botón para enviar la pregunta -->
                <button
                    type="submit"
                    class="btn-enviar"
                >
                    ENVIAR PREGUNTA
                </button>

            </form>

        </div>

    <?php endif; ?>


    <!-- Sección para mostrar las preguntas guardadas -->
    <div class="lista-preguntas">

        <h2>Preguntas</h2>

        <!-- Itera sobre cada pregunta obtenida -->
        <?php foreach ($preguntas as $fila): ?>

            <div class="tarjeta-pregunta">

                <!-- Muestra el nombre del autor de la pregunta -->
                <h3>
                    <?php echo htmlspecialchars($fila["nombre"]); ?>
                </h3>

                <!-- Muestra el contenido de la pregunta -->
                <p>
                    <?php echo htmlspecialchars($fila["pregunta"]); ?>
                </p>

                <?php if (!empty($fila["respuesta"])): ?>
                    <div class="respuesta-pregunta">
                        <strong>Respuesta de <?php echo htmlspecialchars($fila["respondido_por_nombre"] ?: "Milky", ENT_QUOTES, "UTF-8"); ?>:</strong>
                        <p><?php echo nl2br(htmlspecialchars($fila["respuesta"], ENT_QUOTES, "UTF-8")); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($esAdminPreguntas): ?>
                    <form class="form-respuesta" action="controlador/Maria%20A/responder.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenPreguntas, ENT_QUOTES, "UTF-8"); ?>">
                        <input type="hidden" name="id" value="<?php echo (int)$fila["id"]; ?>">
                        <label class="sr-only" for="respuesta-<?php echo (int)$fila["id"]; ?>">Respuesta</label>
                        <textarea id="respuesta-<?php echo (int)$fila["id"]; ?>" name="respuesta" placeholder="Escribe una respuesta..." required><?php echo htmlspecialchars($fila["respuesta"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>
                        <button type="submit" class="btn-guardar">GUARDAR RESPUESTA</button>
                    </form>
                <?php endif; ?>

                <?php if ($esAdminPreguntas): ?>
                <div class="acciones">

                    <!-- Botón para editar la pregunta -->
                    <a
                        href="conocenos.php?editar=<?php echo $fila["id"]; ?>"
                        class="btn-editar"
                    >
                        EDITAR
                    </a>

                    <!-- Botón para eliminar la pregunta -->
                    <form action="controlador/Maria%20A/eliminar.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta pregunta?');">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenPreguntas, ENT_QUOTES, "UTF-8"); ?>">
                        <input type="hidden" name="id" value="<?php echo (int)$fila["id"]; ?>">
                        <button type="submit" class="btn-eliminar">ELIMINAR</button>
                    </form>

                </div>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>