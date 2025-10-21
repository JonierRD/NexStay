<?php
require_once '../database/conexion.php';

class ReservaServicio {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // Obtener todas las relaciones reserva-servicio
    public function obtenerReservaServicios() {
        $sql = "SELECT * FROM reserva_servicio";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una relación por ID
    public function obtenerReservaServicioPorId($id) {
        $sql = "SELECT * FROM reserva_servicio WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nueva relación reserva-servicio
    public function crearReservaServicio($data) {
        $sql = "INSERT INTO reserva_servicio (reserva_id, servicio_id, cantidad, total)
                VALUES (?, ?, ?, ?)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            $data['reserva_id'],
            $data['servicio_id'],
            $data['cantidad'],
            $data['total']
        ]);
    }

    // Actualizar una relación existente
    public function actualizarReservaServicio($id, $data) {
        $sql = "UPDATE reserva_servicio SET 
                    reserva_id = ?, 
                    servicio_id = ?, 
                    cantidad = ?, 
                    total = ?
                WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            $data['reserva_id'],
            $data['servicio_id'],
            $data['cantidad'],
            $data['total'],
            $id
        ]);
    }

    // Eliminar una relación reserva-servicio
    public function eliminarReservaServicio($id) {
        $sql = "DELETE FROM reserva_servicio WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$id]);
    }
}
?>
