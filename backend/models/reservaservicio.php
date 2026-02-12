<?php
require_once __DIR__ . '/../database/conexion.php';

class ReservaServicio {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // ✅ Verificar existencia de reserva
    private function reservaExiste($reserva_id) {
        $stmt = $this->conn->prepare("SELECT id FROM reservas WHERE id = :reserva_id");
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // ✅ Verificar existencia de servicio
    private function servicioExiste($servicio_id) {
        $stmt = $this->conn->prepare("SELECT id FROM servicios WHERE id = :servicio_id");
        $stmt->bindParam(':servicio_id', $servicio_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Obtener todos los registros con detalle del servicio
    public function obtenerReservaServicios() {
        $query = "SELECT rs.id, rs.reserva_id, rs.servicio_id, s.nombre AS servicio_nombre, 
                         s.precio AS servicio_precio, rs.cantidad, rs.total
                  FROM reserva_servicio rs
                  INNER JOIN servicios s ON rs.servicio_id = s.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un registro específico
    public function obtenerReservaServicio($id) {
        $query = "SELECT rs.id, rs.reserva_id, rs.servicio_id, s.nombre AS servicio_nombre,
                         s.precio AS servicio_precio, rs.cantidad, rs.total
                  FROM reserva_servicio rs
                  INNER JOIN servicios s ON rs.servicio_id = s.id
                  WHERE rs.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo registro con validación de integridad y actualización de factura
    public function crearReservaServicio($reserva_id, $servicio_id, $cantidad) {
        try {
            // 1️⃣ Validar existencia
            if (!$this->reservaExiste($reserva_id)) {
                return ["error" => "La reserva no existe."];
            }
            if (!$this->servicioExiste($servicio_id)) {
                return ["error" => "El servicio no existe."];
            }

            $this->conn->beginTransaction();

            // 2️⃣ Obtener precio del servicio
            $stmt_precio = $this->conn->prepare("SELECT nombre, precio FROM servicios WHERE id = :servicio_id");
            $stmt_precio->bindParam(':servicio_id', $servicio_id);
            $stmt_precio->execute();
            $servicio = $stmt_precio->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                $this->conn->rollBack();
                return ["error" => "Servicio no encontrado"];
            }

            $precio = (float)$servicio["precio"];
            $total = $cantidad * $precio;

            // 3️⃣ Insertar en reserva_servicio
            $stmt = $this->conn->prepare("
                INSERT INTO reserva_servicio (reserva_id, servicio_id, cantidad, total)
                VALUES (:reserva_id, :servicio_id, :cantidad, :total)
            ");
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->bindParam(':servicio_id', $servicio_id);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            // 4️⃣ Actualizar factura (si existe)
            $stmt_factura = $this->conn->prepare("
                UPDATE facturas 
                SET monto = monto + :total 
                WHERE reserva_id = :reserva_id
            ");
            $stmt_factura->bindParam(':total', $total);
            $stmt_factura->bindParam(':reserva_id', $reserva_id);
            $stmt_factura->execute();

            $this->conn->commit();

            return [
                "success" => true,
                "reserva_id" => $reserva_id,
                "servicio" => $servicio["nombre"],
                "cantidad" => $cantidad,
                "precio_unitario" => $precio,
                "total" => $total
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["error" => "Error al crear reserva_servicio: " . $e->getMessage()];
        }
    }

    // Actualizar registro (recalcula total)
    public function actualizarReservaServicio($id, $reserva_id, $servicio_id, $cantidad) {
        try {
            // 1️⃣ Validar existencia antes de actualizar
            if (!$this->reservaExiste($reserva_id)) {
                return ["error" => "La reserva no existe."];
            }
            if (!$this->servicioExiste($servicio_id)) {
                return ["error" => "El servicio no existe."];
            }

            $this->conn->beginTransaction();

            $stmt_precio = $this->conn->prepare("SELECT precio FROM servicios WHERE id = :servicio_id");
            $stmt_precio->bindParam(':servicio_id', $servicio_id);
            $stmt_precio->execute();
            $servicio = $stmt_precio->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                $this->conn->rollBack();
                return ["error" => "Servicio no encontrado"];
            }

            $precio = (float)$servicio["precio"];
            $total = $cantidad * $precio;

            $stmt = $this->conn->prepare("
                UPDATE reserva_servicio 
                SET reserva_id = :reserva_id, servicio_id = :servicio_id, 
                    cantidad = :cantidad, total = :total 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->bindParam(':servicio_id', $servicio_id);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            // Actualizar factura
            $stmt_factura = $this->conn->prepare("
                UPDATE facturas 
                SET monto = (SELECT SUM(total) FROM reserva_servicio WHERE reserva_id = :reserva_id)
                WHERE reserva_id = :reserva_id
            ");
            $stmt_factura->bindParam(':reserva_id', $reserva_id);
            $stmt_factura->execute();

            $this->conn->commit();

            return [
                "success" => true,
                "id" => $id,
                "reserva_id" => $reserva_id,
                "servicio_id" => $servicio_id,
                "cantidad" => $cantidad,
                "precio_unitario" => $precio,
                "total" => $total
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["error" => "Error al actualizar reserva_servicio: " . $e->getMessage()];
        }
    }

    // Obtener factura completa con todos los detalles
    public function obtenerFacturaCompleta($reserva_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    r.id AS reserva_id,
                    c.nombre AS cliente,
                    c.correo,
                    h.numero AS habitacion,
                    r.fecha_entrada,
                    r.fecha_salida,
                    r.total AS total_reserva
                FROM reservas r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN habitaciones h ON r.habitacion_id = h.id
                WHERE r.id = :reserva_id
            ");
            $stmt->bindParam(':reserva_id', $reserva_id);
            $stmt->execute();
            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reserva) return ["error" => "Reserva no encontrada"];

            $stmt_serv = $this->conn->prepare("
                SELECT s.nombre AS servicio, s.precio AS precio_unitario, rs.cantidad, rs.total
                FROM reserva_servicio rs
                INNER JOIN servicios s ON rs.servicio_id = s.id
                WHERE rs.reserva_id = :reserva_id
            ");
            $stmt_serv->bindParam(':reserva_id', $reserva_id);
            $stmt_serv->execute();
            $servicios = $stmt_serv->fetchAll(PDO::FETCH_ASSOC);

            $total_servicios = array_sum(array_column($servicios, 'total'));

            $stmt_parq = $this->conn->prepare("
                SELECT COALESCE(SUM(monto), 0) AS total_parqueadero
                FROM facturas
                WHERE reserva_id = :reserva_id
                  AND parqueadero_id IS NOT NULL
            ");
            $stmt_parq->bindParam(':reserva_id', $reserva_id);
            $stmt_parq->execute();
            $parqueadero = $stmt_parq->fetch(PDO::FETCH_ASSOC);
            $total_parqueadero = floatval($parqueadero['total_parqueadero']);

            $total_general = floatval($reserva['total_reserva']) + $total_servicios + $total_parqueadero;

            return [
                "reserva" => $reserva,
                "servicios" => $servicios,
                "total_servicios" => $total_servicios,
                "total_parqueadero" => $total_parqueadero,
                "total_general" => $total_general
            ];

        } catch (PDOException $e) {
            return ["error" => "Error al obtener resumen: " . $e->getMessage()];
        }
    }

    // Eliminar registro y ajustar factura
    public function eliminarReservaServicio($id) {
        try {
            $this->conn->beginTransaction();

            $stmt_info = $this->conn->prepare("SELECT reserva_id, total FROM reserva_servicio WHERE id = :id");
            $stmt_info->bindParam(':id', $id);
            $stmt_info->execute();
            $data = $stmt_info->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                $this->conn->rollBack();
                return ["error" => "Registro no encontrado"];
            }

            $stmt = $this->conn->prepare("DELETE FROM reserva_servicio WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt_factura = $this->conn->prepare("
                UPDATE facturas 
                SET monto = monto - :total
                WHERE reserva_id = :reserva_id
            ");
            $stmt_factura->bindParam(':total', $data["total"]);
            $stmt_factura->bindParam(':reserva_id', $data["reserva_id"]);
            $stmt_factura->execute();

            $this->conn->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["error" => "Error al eliminar reserva_servicio: " . $e->getMessage()];
        }
    }
}
?>
