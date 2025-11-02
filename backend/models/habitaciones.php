<?php
require_once __DIR__ . '/../database/conexion.php';

class Habitaciones {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las habitaciones
    public function obtenerHabitaciones() {
        $stmt = $this->conn->prepare("SELECT * FROM habitaciones");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener habitación por ID
    public function obtenerHabitacion($id) {
        $stmt = $this->conn->prepare("SELECT * FROM habitaciones WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nueva habitación
    public function crearHabitacion($numero, $tipo, $descripcion, $precio, $estado) {
        $stmt = $this->conn->prepare("
            INSERT INTO habitaciones (numero, tipo, descripcion, precio, estado, creado_at)
            VALUES (:numero, :tipo, :descripcion, :precio, :estado, NOW())
        ");
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Actualizar habitación
    public function actualizarHabitacion($id, $numero, $tipo, $descripcion, $precio, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE habitaciones SET
            numero = :numero,
            tipo = :tipo,
            descripcion = :descripcion,
            precio = :precio,
            estado = :estado
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Eliminar habitación
    public function eliminarHabitacion($id) {
        $stmt = $this->conn->prepare("DELETE FROM habitaciones WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
