<?php
require_once __DIR__ . '/../database/conexion.php';

class Parqueadero {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los registros
    public function obtenerParqueadero() {
        $stmt = $this->conn->prepare("SELECT * FROM parqueadero");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un registro por ID
    public function obtenerParqueaderoId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM parqueadero WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insertar un nuevo registro
    public function insertarParqueadero($placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida) {
        $stmt = $this->conn->prepare("
            INSERT INTO parqueadero (placa, cliente_id, habitacion_id, tarifa, fecha_entrada, fecha_salida)
            VALUES (:placa, :cliente_id, :habitacion_id, :tarifa, :fecha_entrada, :fecha_salida)
        ");
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':tarifa', $tarifa);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        return $stmt->execute();
    }

    // Actualizar un registro
    public function actualizarParqueadero($id, $placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida) {
        $stmt = $this->conn->prepare("
            UPDATE parqueadero SET 
                placa = :placa, 
                cliente_id = :cliente_id, 
                habitacion_id = :habitacion_id, 
                tarifa = :tarifa, 
                fecha_entrada = :fecha_entrada, 
                fecha_salida = :fecha_salida
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':tarifa', $tarifa);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        return $stmt->execute();
    }

    // Eliminar un registro
    public function eliminarParqueadero($id) {
        $stmt = $this->conn->prepare("DELETE FROM parqueadero WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
