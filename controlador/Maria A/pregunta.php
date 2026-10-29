<?php

// Requiere el archivo de conexión a la base de datos
require_once __DIR__ . '/../conexion/conexion.php';

// Define la clase Pregunta
class Pregunta
{
    // Propiedad para almacenar la conexión a la base de datos
    private $conexion;

    // Constructor de la clase
    public function __construct()
    {
        global $conexion;
        $this->conexion = $conexion;
    }

    // Método para crear una nueva pregunta
    public function crear($nombre, $pregunta, ?int $usuarioId = null)
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO preguntas (nombre, pregunta, usuario_id)
             VALUES (:nombre, :pregunta, :usuario_id)"
        );

        return $stmt->execute([
            ":nombre" => $nombre,
            ":pregunta" => $pregunta,
            ":usuario_id" => $usuarioId,
        ]);
    }

    // Método para leer todas las preguntas
    public function leer()
    {
        // Consulta SQL para seleccionar todas las preguntas ordenadas por ID de forma descendente
        $stmt = $this->conexion->prepare(
            "SELECT p.*, admin.nombre AS respondido_por_nombre
             FROM preguntas p
             LEFT JOIN usuarios admin ON admin.id = p.respondido_por
             ORDER BY p.id DESC"
        );
        $stmt->execute();

        // Devuelve todas las filas como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para buscar una pregunta por su ID
    public function buscar($id)
    {
        // Consulta SQL para seleccionar una pregunta específica por su ID
        $sql = "SELECT * FROM preguntas WHERE id = :id";

        // Prepara la consulta SQL
        $stmt = $this->conexion->prepare($sql);
        // Asigna el valor al parámetro de la consulta
        $stmt->bindParam(":id", $id);
        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve la fila encontrada como un arreglo asociativo
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para editar una pregunta existente
    public function editar($id, $nombre, $pregunta)
    {
        // Consulta SQL para actualizar una pregunta específica
        $sql = "UPDATE preguntas
                SET nombre = :nombre,
                    pregunta = :pregunta
                WHERE id = :id";

        // Prepara la consulta SQL
        $stmt = $this->conexion->prepare($sql);

        // Asigna los valores a los parámetros de la consulta
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":pregunta", $pregunta);

        // Ejecuta la consulta y devuelve el resultado
        return $stmt->execute();
    }

    public function responder($id, $respuesta, ?int $respondidoPor = null)
    {
        $sql = "UPDATE preguntas
                SET respuesta = :respuesta, respondido_por = :respondido_por
                WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":respuesta", $respuesta);
        $stmt->bindParam(":respondido_por", $respondidoPor);

        return $stmt->execute();
    }

    // Método para eliminar una pregunta por su ID
    public function eliminar($id)
    {
        // Consulta SQL para eliminar una pregunta específica por su ID
        $sql = "DELETE FROM preguntas
                WHERE id = :id";

        // Prepara la consulta SQL
        $stmt = $this->conexion->prepare($sql);
        // Asigna el valor al parámetro de la consulta
        $stmt->bindParam(":id", $id);

        // Ejecuta la consulta y devuelve el resultado
        return $stmt->execute();
    }
}
?>