<?php
require_once "../database/conexion.php";

class Facturas {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // 🔹 Obtener todas las facturas
    public function getFacturas() {
        $sql = "SELECT * FROM facturas ORDER BY id DESC";
        $query = $this->conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener una factura por ID
    public function getFactura($id) {
        $sql = "SELECT * FROM facturas WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Crear una nueva factura
    public function crearFactura($reserva_id, $monto, $metodo_pago, $fecha, $notas) {
        $sql = "INSERT INTO facturas (reserva_id, monto, metodo_pago, fecha, notas)
                VALUES (?, ?, ?, ?, ?)";
        $query = $this->conexion->prepare($sql);
        $query->execute([$reserva_id, $monto, $metodo_pago, $fecha, $notas]);
        return $this->conexion->lastInsertId();
    }

    // 🔹 Actualizar una factura existente
    public function actualizarFactura($id, $reserva_id, $monto, $metodo_pago, $fecha, $notas) {
        $sql = "UPDATE facturas SET reserva_id = ?, monto = ?, metodo_pago = ?, fecha = ?, notas = ?
                WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        return $query->execute([$reserva_id, $monto, $metodo_pago, $fecha, $notas, $id]);
    }

    // 🔹 Eliminar una factura
    public function eliminarFactura($id) {
        $sql = "DELETE FROM facturas WHERE id = ?";
        $query = $this->conexion->prepare($sql);
        return $query->execute([$id]);
    }
}
?>

