<?php
require_once(__DIR__ . '/../database/conexion.php');

class Parqueadero {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // 🔹 Obtener todos los registros de parqueadero
    public function obtenerParqueadero() {
        $query = "SELECT * FROM parqueadero";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Crear un nuevo registro
    public function crearRegistro($placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida) {
        $query = "INSERT INTO parqueadero 
        (placa, cliente_id, habitacion_id, tarifa, fecha_entrada, fecha_salida)
        VALUES 
        (:placa, :cliente_id, :habitacion_id, :tarifa, :fecha_entrada, :fecha_salida)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':tarifa', $tarifa);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        return $stmt->execute();
    }

    // 🔹 Actualizar registro existente
    public function actualizarRegistro($id, $placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida) {
        $query = "UPDATE parqueadero SET 
                    placa = :placa,
                    cliente_id = :cliente_id,
                    habitacion_id = :habitacion_id,
                    tarifa = :tarifa,
                    fecha_entrada = :fecha_entrada,
                    fecha_salida = :fecha_salida
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':tarifa', $tarifa);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        return $stmt->execute();
    }

    // 🔹 Eliminar registro
    public function eliminarRegistro($id) {
        $query = "DELETE FROM parqueadero WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
