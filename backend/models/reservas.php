<?php
require_once __DIR__ . '/../database/conexion.php';

class Reservas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las reservas
    public function obtenerReservas() {
        $stmt = $this->conn->prepare("SELECT * FROM reservas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reserva por ID
    public function obtenerReserva($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reservas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nueva reserva
    public function crearReserva($cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida, $total, $estado) {
        $stmt = $this->conn->prepare("
            INSERT INTO reservas (cliente_id, habitacion_id, fecha_entrada, fecha_salida, total, estado, creado_at)
            VALUES (:cliente_id, :habitacion_id, :fecha_entrada, :fecha_salida, :total, :estado, NOW())
        ");
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Actualizar reserva
    public function actualizarReserva($id, $cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida, $total, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE reservas SET
            cliente_id = :cliente_id,
            habitacion_id = :habitacion_id,
            fecha_entrada = :fecha_entrada,
            fecha_salida = :fecha_salida,
            total = :total,
            estado = :estado
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Eliminar reserva
    public function eliminarReserva($id) {
        $stmt = $this->conn->prepare("DELETE FROM reservas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
