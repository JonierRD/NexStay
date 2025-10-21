<?php
require_once "../database/conexion.php";

class Habitaciones {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // 🔹 Obtener todas las habitaciones
    public function getHabitaciones() {
        $sql = "SELECT * FROM habitaciones ORDER BY id DESC";
        $query = $this->conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener una habitación por ID
    public function getHabitacion($id) {
        $sql = "SELECT * FROM habitaciones WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Crear una nueva habitación
    public function crearHabitacion($numero, $tipo, $descripcion, $precio, $estado, $creado_at) {
        $sql = "INSERT INTO habitaciones (numero, tipo, descripcion, precio, estado, creado_at)
                VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->conexion->prepare($sql);
        $query->execute([$numero, $tipo, $descripcion, $precio, $estado, $creado_at]);
        return $this->conexion->lastInsertId();
    }

    // 🔹 Actualizar una habitación existente
    public function actualizarHabitacion($id, $numero, $tipo, $descripcion, $precio, $estado) {
        $sql = "UPDATE habitaciones 
                SET numero = ?, tipo = ?, descripcion = ?, precio = ?, estado = ?
                WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        return $query->execute([$numero, $tipo, $descripcion, $precio, $estado, $id]);
    }

    // 🔹 Eliminar una habitación
    public function eliminarHabitacion($id) {
        $sql = "DELETE FROM habitaciones WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        return $query->execute([$id]);
    }
}
?>
