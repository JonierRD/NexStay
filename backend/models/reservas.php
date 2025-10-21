<?php
require_once(__DIR__ . '/../database/conexion.php');

class Reservas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // 🔹 Obtener todas las reservas
    public function obtenerReservas() {
        $query = "SELECT * FROM reservas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Crear una nueva reserva
    public function crearReserva($cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida, $total, $estado) {
        $query = "INSERT INTO reservas 
        (cliente_id, habitacion_id, fecha_entrada, fecha_salida, total, estado, creado_at)
        VALUES 
        (:cliente_id, :habitacion_id, :fecha_entrada, :fecha_salida, :total, :estado, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // 🔹 Actualizar una reserva existente
    public function actualizarReserva($id, $cliente_id, $habitacion_id, $fecha_entrada, $_
