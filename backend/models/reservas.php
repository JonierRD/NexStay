<?php
require_once __DIR__ . '/../database/conexion.php';

class Reservas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las reservas
    public function obtenerReservas() {
        $stmt = $this->conn->prepare("SELECT * FROM reservas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reserva por ID
    public function obtenerReserva($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reservas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar solapamiento de fechas en estados activos
    private function verificarSolapamiento($habitacion_id, $fecha_entrada, $fecha_salida) {
        $query = "SELECT COUNT(*) as total 
                  FROM reservas 
                  WHERE habitacion_id = :habitacion_id
                  AND estado IN ('confirmada', 'checkin')
                  AND (
                        (fecha_entrada <= :fecha_salida AND fecha_salida >= :fecha_entrada)
                      )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }

    // Verificar si una habitación está libre
    private function habitacionDisponible($habitacion_id) {
        $stmt = $this->conn->prepare("SELECT estado FROM habitaciones WHERE id = :habitacion_id");
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->execute();
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        return $habitacion && strtolower(trim($habitacion['estado'])) === 'disponible';
    }

    // Cambiar estado de habitación
    private function actualizarEstadoHabitacion($habitacion_id, $estado) {
        $stmt = $this->conn->prepare("UPDATE habitaciones SET estado = :estado WHERE id = :habitacion_id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->execute();
    }

    private function estadoHabitacionPorReserva($estadoReserva) {
        $estado = strtolower(trim((string)$estadoReserva));
        if ($estado === 'checkin') {
            return 'ocupada';
        }
        if ($estado === 'checkout' || $estado === 'cancelada') {
            return 'disponible';
        }
        return 'reservada'; // confirmada
    }

    // Crear nueva reserva
    public function crearReserva($cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida, $total, $estado) {
        // 1️⃣ Validar disponibilidad
        if (!$this->habitacionDisponible($habitacion_id)) {
            return ["error" => "La habitación seleccionada no está disponible."];
        }

        // 2️⃣ Verificar solapamiento de fechas
        if ($this->verificarSolapamiento($habitacion_id, $fecha_entrada, $fecha_salida)) {
            return ["error" => "La habitación ya está reservada en las fechas seleccionadas."];
        }

        // 3️⃣ Crear reserva si todo está correcto
        $stmt = $this->conn->prepare("
            INSERT INTO reservas (cliente_id, habitacion_id, fecha_entrada, fecha_salida, total, estado, creado_at)
            VALUES (:cliente_id, :habitacion_id, :fecha_entrada, :fecha_salida, :total, :estado, NOW())
        ");
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            // Sincronizar estado de habitación con el estado real de la reserva
            $this->actualizarEstadoHabitacion($habitacion_id, $this->estadoHabitacionPorReserva($estado));
            return ["success" => true];
        }

        return ["error" => "No se pudo crear la reserva."];
    }

    // Actualizar reserva
    public function actualizarReserva($id, $cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida, $total, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE reservas SET
            cliente_id = :cliente_id,
            habitacion_id = :habitacion_id,
            fecha_entrada = :fecha_entrada,
            fecha_salida = :fecha_salida,
            total = :total,
            estado = :estado
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);
        $resultado = $stmt->execute();

        // Sincronizar estado de habitación con estado de reserva compatible con ENUM SQL
        $this->actualizarEstadoHabitacion($habitacion_id, $this->estadoHabitacionPorReserva($estado));

        return ["success" => $resultado];
    }

    // ❌ Eliminar reserva
    public function eliminarReserva($id) {
        // Obtener reserva antes de eliminar
        $reserva = $this->obtenerReserva($id);
        if (!$reserva) return ["error" => "Reserva no encontrada"];

        $stmt = $this->conn->prepare("DELETE FROM reservas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $resultado = $stmt->execute();

        // Liberar habitación al eliminar reserva
        if ($resultado && isset($reserva['habitacion_id'])) {
            $this->actualizarEstadoHabitacion($reserva['habitacion_id'], 'disponible');
        }

        return ["success" => $resultado];
    }
}
?>
