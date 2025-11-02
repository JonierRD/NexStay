<?php
require_once __DIR__ . '/../database/conexion.php';

class Facturas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las facturas
    public function obtenerFacturas() {
        $query = "SELECT * FROM facturas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una factura por ID
    public function obtenerFactura($id) {
        $query = "SELECT * FROM facturas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearFactura($reserva_id, $monto, $metodo_pago, $fecha, $notas) {
    $query = "INSERT INTO facturas 
              (reserva_id, monto, metodo_pago, fecha, notas) 
              VALUES 
              (:reserva_id, :monto, :metodo_pago, :fecha, :notas)";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':reserva_id', $reserva_id);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':metodo_pago', $metodo_pago);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':notas', $notas);

    return $stmt->execute();
}


    // Actualizar una factura
    public function actualizarFactura($id, $reserva_id, $monto, $metodo_pago, $fecha, $notas) {
        $query = "UPDATE facturas SET 
                    reserva_id = :reserva_id,
                    monto = :monto,
                    metodo_pago = :metodo_pago,
                    fecha = :fecha,
                    notas = :notas
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':metodo_pago', $metodo_pago);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':notas', $notas);
        return $stmt->execute();
    }

    // Eliminar una factura
    public function eliminarFactura($id) {
        $query = "DELETE FROM facturas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    //generarfactura por reserva
    public function generarFacturaPorReserva($reserva_id, $metodo_pago = "efectivo", $notas = "") {
    $stmt = $this->conn->prepare("SELECT total FROM reservas WHERE id = :reserva_id");
    $stmt->bindParam(':reserva_id', $reserva_id);
    $stmt->execute();
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reserva) return false;

    $total = $reserva['total'];

    $stmt2 = $this->conn->prepare("
        INSERT INTO facturas (reserva_id, monto, metodo_pago, fecha, notas)
        VALUES (:reserva_id, :monto, :metodo_pago, NOW(), :notas)
    ");
    $stmt2->bindParam(':reserva_id', $reserva_id);
    $stmt2->bindParam(':monto', $total);
    $stmt2->bindParam(':metodo_pago', $metodo_pago);
    $stmt2->bindParam(':notas', $notas);
    return $stmt2->execute();
}

    // Obtener factura completa con detalles
public function obtenerFacturaCompleta($id) {
    $stmt = $this->conn->prepare("
        SELECT f.id AS factura_id, f.monto, f.metodo_pago, f.fecha, f.notas,
               c.nombre AS cliente, h.tipo AS tipo_habitacion, h.numero AS numero_habitacion,
               GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios,
               p.placa AS parqueadero
        FROM facturas f
        LEFT JOIN reservas r ON f.reserva_id = r.id
        LEFT JOIN clientes c ON r.cliente_id = c.id
        LEFT JOIN habitaciones h ON r.habitacion_id = h.id
        LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
        LEFT JOIN servicios s ON rs.servicio_id = s.id
        LEFT JOIN parqueadero p ON r.id = p.reserva_id
        WHERE f.id = :id
        GROUP BY f.id
    ");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



}
?>
