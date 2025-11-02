<?php
require_once __DIR__ . '/../database/conexion.php';

class Mantenimientos {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los registros de mantenimiento
    public function obtenerMantenimientos() {
        $query = "SELECT * FROM mantenimientos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener un mantenimiento por ID
public function obtenerMantenimiento($id) {
    $query = "SELECT * FROM mantenimientos WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un solo registro
}


    // Crear nuevo mantenimiento
    public function crearMantenimiento($habitacion_id, $descripcion, $estado, $fecha_reporte, $fecha_solucion, $empleado_id) {
        $query = "INSERT INTO mantenimientos 
        (habitacion_id, descripcion, estado, fecha_reporte, fecha_solucion, empleado_id)
        VALUES 
        (:habitacion_id, :descripcion, :estado, :fecha_reporte, :fecha_solucion, :empleado_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':fecha_reporte', $fecha_reporte);
        $stmt->bindParam(':fecha_solucion', $fecha_solucion);
        $stmt->bindParam(':empleado_id', $empleado_id);

        return $stmt->execute();
    }

    // Actualizar mantenimiento
    public function actualizarMantenimiento($id, $habitacion_id, $descripcion, $estado, $fecha_reporte, $fecha_solucion, $empleado_id) {
        $query = "UPDATE mantenimientos SET 
                    habitacion_id = :habitacion_id,
                    descripcion = :descripcion,
                    estado = :estado,
                    fecha_reporte = :fecha_reporte,
                    fecha_solucion = :fecha_solucion,
                    empleado_id = :empleado_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':fecha_reporte', $fecha_reporte);
        $stmt->bindParam(':fecha_solucion', $fecha_solucion);
        $stmt->bindParam(':empleado_id', $empleado_id);

        return $stmt->execute();
    }

    // Eliminar mantenimiento
    public function eliminarMantenimiento($id) {
        $query = "DELETE FROM mantenimientos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
