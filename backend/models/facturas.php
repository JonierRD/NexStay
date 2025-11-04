<?php
require_once __DIR__ . '/../database/conexion.php';

class Facturas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // 🧾 Obtener todas las facturas
    public function obtenerFacturas() {
        try {
            $query = "SELECT * FROM facturas";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // 🧾 Obtener una factura por ID
    public function obtenerFactura($id) {
        try {
            $query = "SELECT * FROM facturas WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // ➕ Crear factura manual
    public function crearFactura($reserva_id, $monto, $metodo_pago, $fecha = null, $notas = "") {
        try {
            $fecha = $fecha ?? date("Y-m-d H:i:s");
            $query = "INSERT INTO facturas (reserva_id, monto, metodo_pago, fecha, notas)
                      VALUES (:reserva_id, :monto, :metodo_pago, :fecha, :notas)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':metodo_pago', $metodo_pago);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':notas', $notas);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // ✏️ Actualizar factura
    public function actualizarFactura($id, $reserva_id, $monto, $metodo_pago, $fecha = null, $notas = "") {
        try {
            $fecha = $fecha ?? date("Y-m-d H:i:s");
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
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // ❌ Eliminar factura
    public function eliminarFactura($id) {
        try {
            $query = "DELETE FROM facturas WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // ⚙️ Generar factura automática por reserva (con validaciones)
    public function generarFacturaPorReserva($reserva_id, $metodo_pago = "efectivo", $notas = "") {
        try {
            // 1️⃣ Validar si ya existe una factura para esta reserva
            $check = $this->conn->prepare("SELECT id FROM facturas WHERE reserva_id = :reserva_id");
            $check->bindParam(':reserva_id', $reserva_id);
            $check->execute();
            if ($check->fetch()) {
                return ["error" => "Ya existe una factura para esta reserva"];
            }

            // 2️⃣ Obtener total base de la reserva
            $stmt = $this->conn->prepare("SELECT total FROM reservas WHERE id = :reserva_id");
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->execute();
            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$reserva) return ["error" => "Reserva no encontrada"];
            $total_reserva = floatval($reserva['total']);

            // 3️⃣ Total de servicios adicionales
            $stmt2 = $this->conn->prepare("
                SELECT COALESCE(SUM(total), 0) AS total_servicios 
                FROM reserva_servicio 
                WHERE reserva_id = :reserva_id
            ");
            $stmt2->bindParam(':reserva_id', $reserva_id);
            $stmt2->execute();
            $total_servicios = floatval($stmt2->fetch(PDO::FETCH_ASSOC)['total_servicios']);

            // 4️⃣ Total del parqueadero (según días y tarifa)
            $stmt3 = $this->conn->prepare("
                SELECT 
                    COALESCE(SUM(
                        DATEDIFF(fecha_salida, fecha_entrada) * tarifa
                    ), 0) AS total_parqueadero
                FROM parqueadero
                WHERE habitacion_id IN (SELECT habitacion_id FROM reservas WHERE id = :reserva_id)
            ");
            $stmt3->bindParam(':reserva_id', $reserva_id);
            $stmt3->execute();
            $total_parqueadero = floatval($stmt3->fetch(PDO::FETCH_ASSOC)['total_parqueadero']);

            // 5️⃣ Calcular total final
            $total_final = $total_reserva + $total_servicios + $total_parqueadero;

            // 6️⃣ Insertar factura final
            $stmt4 = $this->conn->prepare("
                INSERT INTO facturas (reserva_id, monto, metodo_pago, fecha, notas)
                VALUES (:reserva_id, :monto, :metodo_pago, NOW(), :notas)
            ");
            $stmt4->bindParam(':reserva_id', $reserva_id);
            $stmt4->bindParam(':monto', $total_final);
            $stmt4->bindParam(':metodo_pago', $metodo_pago);
            $stmt4->bindParam(':notas', $notas);
            $stmt4->execute();

            return ["success" => true, "monto_total" => $total_final];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // 📊 Obtener factura completa (resumen financiero)
    public function obtenerFacturaCompleta($reserva_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    r.id AS reserva_id,
                    c.nombre AS cliente,
                    h.numero AS habitacion,
                    r.fecha_entrada, 
                    r.fecha_salida,
                    r.total AS total_reserva,

                    COALESCE(SUM(rs.total), 0) AS total_servicios,
                    COALESCE(SUM(p.tarifa * DATEDIFF(p.fecha_salida, p.fecha_entrada)), 0) AS total_parqueadero,

                    (r.total 
                    + COALESCE(SUM(rs.total), 0)
                    + COALESCE(SUM(p.tarifa * DATEDIFF(p.fecha_salida, p.fecha_entrada)), 0)) AS total_general

                FROM reservas r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN habitaciones h ON r.habitacion_id = h.id
                LEFT JOIN reserva_servicio rs ON r.id = rs.reserva_id
                LEFT JOIN parqueadero p ON r.habitacion_id = p.habitacion_id
                WHERE r.id = :reserva_id
                GROUP BY r.id
            ");
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: ["error" => "No se encontró información para esa reserva"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
