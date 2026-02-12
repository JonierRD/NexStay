<?php
require_once __DIR__ . '/../database/conexion.php';

class Hospedajes {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los hospedajes
    public function obtenerHospedajes() {
        $query = "SELECT h.*, c.nombre AS cliente, hab.numero AS habitacion
                  FROM hospedajes h
                  INNER JOIN clientes c ON h.cliente_id = c.id
                  INNER JOIN habitaciones hab ON h.habitacion_id = hab.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un hospedaje por ID
    public function obtenerHospedaje($id) {
        $query = "SELECT * FROM hospedajes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function crearHospedaje($cliente_id, $habitacion_id, $fecha_entrada) {
    try {
        $this->conn->beginTransaction();

        // 1) Verificar que la habitación exista y no esté marcada como 'ocupada'
        $check = $this->conn->prepare("SELECT estado FROM habitaciones WHERE id = :id FOR UPDATE");
        $check->bindParam(":id", $habitacion_id);
        $check->execute();
        $row = $check->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $this->conn->rollBack();
            return false; // habitación no existe
        }

        if (isset($row['estado']) && strtolower($row['estado']) === 'ocupada') {
            $this->conn->rollBack();
            return false; // ya ocupada
        }

        // 2) Insert hospdaje
        $query = "INSERT INTO hospedajes (cliente_id, habitacion_id, fecha_entrada, creado_at)
                  VALUES (:cliente_id, :habitacion_id, :fecha_entrada, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":habitacion_id", $habitacion_id);
        $stmt->bindParam(":fecha_entrada", $fecha_entrada);
        $stmt->execute();

        // 3) Marcar habitación como ocupada
        $update = $this->conn->prepare(
            "UPDATE habitaciones SET estado = 'ocupada' WHERE id = :id"
        );
        $update->bindParam(":id", $habitacion_id);
        $update->execute();

        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        // error_log($e->getMessage());
        return false;
    }
}

    // Actualizar hospedaje + manejo de cambio de habitación
    public function actualizarHospedaje($id, $cliente_id, $habitacion_id, $fecha_entrada, $fecha_salida = null) {
        try {
            $this->conn->beginTransaction();

            // Obtener habitación anterior
            $sqlOld = $this->conn->prepare("SELECT habitacion_id FROM hospedajes WHERE id = :id");
            $sqlOld->bindParam(":id", $id);
            $sqlOld->execute();
            $oldRoom = $sqlOld->fetchColumn();

            // Actualizar datos del hospedaje
            $query = "UPDATE hospedajes 
                      SET cliente_id = :cliente_id,
                          habitacion_id = :habitacion_id,
                          fecha_entrada = :fecha_entrada,
                          fecha_salida = :fecha_salida
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":cliente_id", $cliente_id);
            $stmt->bindParam(":habitacion_id", $habitacion_id);
            $stmt->bindParam(":fecha_entrada", $fecha_entrada);
            $stmt->bindParam(":fecha_salida", $fecha_salida);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // Si cambió la habitación
            if ($oldRoom != $habitacion_id) {

                // Liberar habitación anterior
                if ($oldRoom) {
                    $this->conn->prepare("UPDATE habitaciones SET estado = 'disponible' WHERE id = :id")
                               ->execute([$oldRoom]);
                }

                // Ocupar la nueva habitación
                $this->conn->prepare("UPDATE habitaciones SET estado = 'ocupada' WHERE id = :id")
                           ->execute([$habitacion_id]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Finalizar hospedaje + liberar habitación
    public function finalizarHospedaje($id, $fecha_salida) {
        try {
            $this->conn->beginTransaction();

            // Obtener habitación del hospedaje
            $query = "SELECT habitacion_id FROM hospedajes WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $hospedaje = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$hospedaje) return false;

            $habitacion_id = $hospedaje["habitacion_id"];

            // Registrar fecha de salida
            $updateHosp = $this->conn->prepare(
                "UPDATE hospedajes SET fecha_salida = :fecha_salida WHERE id = :id"
            );
            $updateHosp->bindParam(":fecha_salida", $fecha_salida);
            $updateHosp->bindParam(":id", $id);
            $updateHosp->execute();

            // Liberar habitación
            $updateHab = $this->conn->prepare(
                "UPDATE habitaciones SET estado = 'disponible' WHERE id = :id"
            );
            $updateHab->bindParam(":id", $habitacion_id);
            $updateHab->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Eliminar hospedaje + liberar habitación
    public function eliminarHospedaje($id) {
        try {
            $this->conn->beginTransaction();

            // Obtener la habitación del hospedaje
            $query = "SELECT habitacion_id FROM hospedajes WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) return false;

            $habitacion_id = $data["habitacion_id"];

            // Eliminar hospedaje
            $del = $this->conn->prepare("DELETE FROM hospedajes WHERE id = :id");
            $del->bindParam(":id", $id);
            $del->execute();

            // Liberar habitación
            $update = $this->conn->prepare(
                "UPDATE habitaciones SET estado = 'disponible' WHERE id = :id"
            );
            $update->bindParam(":id", $habitacion_id);
            $update->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
