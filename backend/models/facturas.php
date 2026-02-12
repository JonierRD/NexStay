<?php
require_once __DIR__ . '/../database/conexion.php';

class Facturas {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    /* ============================================================
       🧾 OBTENER FACTURAS
    ============================================================ */

    public function obtenerFacturas() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM facturas");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtenerFactura($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM facturas WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /* ============================================================
       ➕ CREAR FACTURA MANUAL
    ============================================================ */

    public function crearFactura($reserva_id, $parqueadero_id, $monto, $metodo_pago, $fecha = null, $notas = "") {
        try {
            $fecha = $fecha ?? date("Y-m-d H:i:s");

            $query = "INSERT INTO facturas (reserva_id, parqueadero_id, monto, metodo_pago, fecha, notas)
                      VALUES (:reserva_id, :parqueadero_id, :monto, :metodo_pago, :fecha, :notas)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->bindParam(':parqueadero_id', $parqueadero_id);
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

    /* ============================================================
       ✏️ ACTUALIZAR FACTURA
    ============================================================ */

    public function actualizarFactura($id, $reserva_id, $parqueadero_id, $monto, $metodo_pago, $fecha = null, $notas = "") {
        try {
            $fecha = $fecha ?? date("Y-m-d H:i:s");

            $query = "UPDATE facturas SET 
                        reserva_id = :reserva_id,
                        parqueadero_id = :parqueadero_id,
                        monto = :monto,
                        metodo_pago = :metodo_pago,
                        fecha = :fecha,
                        notas = :notas
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->bindParam(':parqueadero_id', $parqueadero_id);
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

    /* ============================================================
       ❌ ELIMINAR FACTURA
    ============================================================ */

    public function eliminarFactura($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM facturas WHERE id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /* ============================================================
       ⚙️ GENERAR FACTURA POR RESERVA
    ============================================================ */

    public function generarFacturaPorReserva($reserva_id, $metodo_pago = "efectivo", $notas = "") {
        try {
            // Verificar si ya existe factura
            $check = $this->conn->prepare("SELECT id FROM facturas WHERE reserva_id = :reserva_id");
            $check->bindParam(':reserva_id', $reserva_id);
            $check->execute();

            if ($check->fetch()) {
                return ["error" => "Ya existe una factura para esta reserva"];
            }

            // Obtener total habitación
            $stmt = $this->conn->prepare("SELECT total FROM reservas WHERE id = :reserva_id");
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->execute();

            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$reserva) return ["error" => "Reserva no encontrada"];

            $total_reserva = floatval($reserva['total']);

            // Total servicios adicionales
            $stmt2 = $this->conn->prepare("
                SELECT COALESCE(SUM(total), 0) AS total_servicios 
                FROM reserva_servicio 
                WHERE reserva_id = :reserva_id
            ");
            $stmt2->bindParam(':reserva_id', $reserva_id);
            $stmt2->execute();

            $total_servicios = floatval($stmt2->fetch(PDO::FETCH_ASSOC)['total_servicios']);

            // Total final
            $total_final = $total_reserva + $total_servicios;

            // Crear factura
            $stmt4 = $this->conn->prepare("
                INSERT INTO facturas (reserva_id, parqueadero_id, monto, metodo_pago, fecha, notas)
                VALUES (:reserva_id, NULL, :monto, :metodo_pago, NOW(), :notas)
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

    /* ============================================================
       ⚙️ FACTURA SOLO DE PARQUEADERO
    ============================================================ */

    public function generarFacturaPorParqueadero($parqueadero_id, $metodo_pago = "efectivo", $notas = "") {
        try {
            $q = $this->conn->prepare("
                SELECT fecha_entrada, fecha_salida, tarifa 
                FROM parqueadero 
                WHERE id = :pid
            ");
            $q->bindParam(':pid', $parqueadero_id);
            $q->execute();

            $p = $q->fetch(PDO::FETCH_ASSOC);
            if (!$p) return ["error" => "Registro de parqueadero no encontrado"];

            if ($p["fecha_salida"] == null)
                return ["error" => "No se puede facturar: el vehículo no ha salido"];

            // Calcular horas
            $h1 = strtotime($p["fecha_entrada"]);
            $h2 = strtotime($p["fecha_salida"]);
            $horas = ceil(($h2 - $h1) / 3600);

            $monto = $horas * floatval($p["tarifa"]);

            // Insertar factura
            $stmt = $this->conn->prepare("
                INSERT INTO facturas (reserva_id, parqueadero_id, monto, metodo_pago, fecha, notas)
                VALUES (NULL, :pid, :monto, :metodo_pago, NOW(), :notas)
            ");

            $stmt->bindParam(':pid', $parqueadero_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':metodo_pago', $metodo_pago);
            $stmt->bindParam(':notas', $notas);
            $stmt->execute();

            return [
                "success" => true,
                "horas" => $horas,
                "monto_total" => $monto
            ];

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
