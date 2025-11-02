<?php
require_once __DIR__ . '/../database/conexion.php';

class ReservaServicio {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los registros
    public function obtenerReservaServicios() {
        $query = "SELECT * FROM reserva_servicio";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por ID
    public function obtenerReservaServicio($id) {
        $query = "SELECT * FROM reserva_servicio WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo registro
    public function crearReservaServicio($reserva_id, $servicio_id, $cantidad, $total) {
        $query = "INSERT INTO reserva_servicio (reserva_id, servicio_id, cantidad, total) 
                  VALUES (:reserva_id, :servicio_id, :cantidad, :total)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->bindParam(':servicio_id', $servicio_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':total', $total);
        return $stmt->execute();
    }

    // Actualizar registro
    public function actualizarReservaServicio($id, $reserva_id, $servicio_id, $cantidad, $total) {
        $query = "UPDATE reserva_servicio SET 
                    reserva_id = :reserva_id, 
                    servicio_id = :servicio_id, 
                    cantidad = :cantidad, 
                    total = :total 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->bindParam(':servicio_id', $servicio_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':total', $total);
        return $stmt->execute();
    }

    // Eliminar registro
    public function eliminarReservaServicio($id) {
        $query = "DELETE FROM reserva_servicio WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
