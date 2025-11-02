<?php
require_once(__DIR__ . '/../database/conexion.php');

class Empleados {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los empleados
    public function obtenerEmpleados() {
        $query = "SELECT * FROM empleados";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener un empleado por ID
public function obtenerEmpleado($id) {
    $query = "SELECT * FROM empleados WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // devuelve un solo registro
}


    // Crear un nuevo empleado
    public function crearEmpleado($nombre, $documento, $cargo, $correo, $telefono, $horario) {
        $query = "INSERT INTO empleados 
        (nombre, documento, cargo, correo, telefono, horario, creado_at) 
        VALUES 
        (:nombre, :documento, :cargo, :correo, :telefono, :horario, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':horario', $horario);
        return $stmt->execute();
    }

    // Actualizar empleado
    public function actualizarEmpleado($id, $nombre, $documento, $cargo, $correo, $telefono, $horario) {
        $query = "UPDATE empleados SET 
                    nombre = :nombre,
                    documento = :documento,
                    cargo = :cargo,
                    correo = :correo,
                    telefono = :telefono,
                    horario = :horario
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':horario', $horario);
        return $stmt->execute();
    }

    // Eliminar empleado
    public function eliminarEmpleado($id) {
        $query = "DELETE FROM empleados WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
