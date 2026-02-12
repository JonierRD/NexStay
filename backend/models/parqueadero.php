<?php
require_once __DIR__ . '/../database/conexion.php';

class Parqueadero {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // =====================================================
    // 🔵 VALIDACIÓN ANTI-SOLAPAMIENTOS
    // =====================================================
    private function placaOcupada($placa, $fecha_entrada, $fecha_salida, $id = null) {
        $query = "
            SELECT * FROM parqueadero
            WHERE placa = :placa
            AND (
                (fecha_salida IS NULL)
                OR
                (fecha_entrada <= :fecha_salida AND fecha_salida >= :fecha_entrada)
            )
        ";

        if ($id !== null) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':fecha_salida', $fecha_salida);

        if ($id !== null) {
            $stmt->bindParam(':id', $id);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // 🔵 CRUD BÁSICOS
    // =====================================================
    public function obtenerParqueadero() {
        $stmt = $this->conn->prepare("SELECT * FROM parqueadero ORDER BY fecha_entrada DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerParqueaderoId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM parqueadero WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarParqueadero($nombre_cliente, $placa, $tipo_vehiculo, $fecha_entrada, $fecha_salida, $tarifa, $observaciones) {
        try {
            // Validar solapamiento
            $choque = $this->placaOcupada($placa, $fecha_entrada, $fecha_salida);
            if ($choque) {
                return ["error" => "La placa ya tiene un registro activo o con fechas que se solapan."];
            }

            $stmt = $this->conn->prepare("
                INSERT INTO parqueadero (nombre_cliente, placa, tipo_vehiculo, fecha_entrada, fecha_salida, tarifa, observaciones)
                VALUES (:nombre_cliente, :placa, :tipo_vehiculo, :fecha_entrada, :fecha_salida, :tarifa, :observaciones)
            ");
            $stmt->bindParam(':nombre_cliente', $nombre_cliente);
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':tipo_vehiculo', $tipo_vehiculo);
            $stmt->bindParam(':fecha_entrada', $fecha_entrada);
            $stmt->bindParam(':fecha_salida', $fecha_salida);
            $stmt->bindParam(':tarifa', $tarifa);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            return ["success" => true, "mensaje" => "Registro de parqueadero creado correctamente."];
        } catch (PDOException $e) {
            return ["error" => "Error al insertar: " . $e->getMessage()];
        }
    }

    public function actualizarParqueadero($id, $nombre_cliente, $placa, $tipo_vehiculo, $fecha_entrada, $fecha_salida, $tarifa, $observaciones) {
        try {
            // Validar solapamiento (ignora este registro)
            $choque = $this->placaOcupada($placa, $fecha_entrada, $fecha_salida, $id);
            if ($choque) {
                return ["error" => "Las fechas o la placa generan solapamiento con otro registro."];
            }

            $stmt = $this->conn->prepare("
                UPDATE parqueadero SET 
                    nombre_cliente = :nombre_cliente,
                    placa = :placa,
                    tipo_vehiculo = :tipo_vehiculo,
                    fecha_entrada = :fecha_entrada,
                    fecha_salida = :fecha_salida,
                    tarifa = :tarifa,
                    observaciones = :observaciones
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre_cliente', $nombre_cliente);
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':tipo_vehiculo', $tipo_vehiculo);
            $stmt->bindParam(':fecha_entrada', $fecha_entrada);
            $stmt->bindParam(':fecha_salida', $fecha_salida);
            $stmt->bindParam(':tarifa', $tarifa);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            return ["success" => true, "mensaje" => "Registro actualizado correctamente."];
        } catch (PDOException $e) {
            return ["error" => "Error al actualizar: " . $e->getMessage()];
        }
    }

    // =====================================================
    // 🔵 FACTURAR PARQUEADERO POR DÍAS
    // =====================================================
    public function generarFacturaParqueadero($id, $metodo_pago, $notas) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM parqueadero WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) return ["error" => "El registro de parqueadero no existe."];
            if (!$registro["fecha_salida"]) return ["error" => "El cliente aún no ha registrado salida."];

            // Verificar factura existente
            $check = $this->conn->prepare("SELECT * FROM facturas WHERE parqueadero_id = :id");
            $check->bindParam(':id', $id);
            $check->execute();
            if ($check->fetch()) return ["error" => "Ya existe una factura generada para este parqueadero."];

            $entrada = new DateTime($registro["fecha_entrada"]);
            $salida  = new DateTime($registro["fecha_salida"]);
            $diff = $entrada->diff($salida);

            $dias = $diff->days;
            if ($diff->h > 0 || $diff->i > 0 || $diff->s > 0) $dias++;
            if ($dias < 1) $dias = 1;

            $tarifa = floatval($registro["tarifa"]);
            $total = $dias * $tarifa;

            $insert = $this->conn->prepare("
                INSERT INTO facturas (reserva_id, parqueadero_id, monto, metodo_pago, fecha, notas)
                VALUES (NULL, :parqueadero_id, :monto, :metodo_pago, NOW(), :notas)
            ");
            $insert->bindParam(':parqueadero_id', $id);
            $insert->bindParam(':monto', $total);
            $insert->bindParam(':metodo_pago', $metodo_pago);
            $insert->bindParam(':notas', $notas);
            $insert->execute();

            return [
                "success" => true,
                "mensaje" => "Factura generada correctamente.",
                "parqueadero_id" => $id,
                "dias_cobrados" => $dias,
                "tarifa_por_dia" => $tarifa,
                "total_cobrado" => $total
            ];

        } catch (PDOException $e) {
            return ["error" => "Error al generar la factura: " . $e->getMessage()];
        }
    }

    // =====================================================
    // 🔵 ELIMINAR
    // =====================================================
    public function eliminarParqueadero($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM parqueadero WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return ["success" => true, "mensaje" => "Registro eliminado correctamente."];
        } catch (PDOException $e) {
            return ["error" => "Error al eliminar: " . $e->getMessage()];
        }
    }
}
?>
