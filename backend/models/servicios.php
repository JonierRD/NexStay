<?php
require_once __DIR__ . '/../database/conexion.php';

class Servicios {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los servicios
    public function obtenerServicios() {
        $stmt = $this->conn->prepare("SELECT * FROM servicios");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un servicio por ID
    public function obtenerServicioId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM servicios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insertar un servicio
    public function insertarServicio($nombre, $descripcion, $precio, $disponible) {
        $stmt = $this->conn->prepare("
            INSERT INTO servicios (nombre, descripcion, precio, disponible)
            VALUES (:nombre, :descripcion, :precio, :disponible)
        ");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':disponible', $disponible);
        return $stmt->execute();
    }

    // Actualizar un servicio
    public function actualizarServicio($id, $nombre, $descripcion, $precio, $disponible) {
        $stmt = $this->conn->prepare("
            UPDATE servicios SET 
                nombre = :nombre, 
                descripcion = :descripcion, 
                precio = :precio, 
                disponible = :disponible
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':disponible', $disponible);
        return $stmt->execute();
    }

    // Eliminar un servicio
    public function eliminarServicio($id) {
        $stmt = $this->conn->prepare("DELETE FROM servicios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
