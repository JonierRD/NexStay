<?php
require_once '../database/conexion.php';

class Servicios {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // Obtener todos los servicios
    public function obtenerServicios() {
        $sql = "SELECT * FROM servicios";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un servicio por ID
    public function obtenerServicioPorId($id) {
        $sql = "SELECT * FROM servicios WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo servicio
    public function crearServicio($data) {
        $sql = "INSERT INTO servicios (nombre, descripcion, precio, disponible)
                VALUES (?, ?, ?, ?)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['disponible']
        ]);
    }

    // Actualizar un servicio existente
    public function actualizarServicio($id, $data) {
        $sql = "UPDATE servicios SET 
                    nombre = ?, 
                    descripcion = ?, 
                    precio = ?, 
                    disponible = ?
                WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['disponible'],
            $id
        ]);
    }

    // Eliminar un servicio
    public function eliminarServicio($id) {
        $sql = "DELETE FROM servicios WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$id]);
    }
}
?>
