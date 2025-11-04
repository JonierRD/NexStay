<?php
require_once "../models/ReservaServicio.php";

header("Content-Type: application/json");
$reservaservicio = new ReservaServicio();
$op = $_GET["op"] ?? "";

switch ($op) {
    case 'obtener':
        echo json_encode($reservaservicio->obtenerReservaServicios());
        break;

    case 'obtener_id':
        $id = $_GET["id"] ?? null;
        echo json_encode($id ? $reservaservicio->obtenerReservaServicio($id) : ["error" => "Falta el parámetro ID"]);
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["reserva_id"], $data["servicio_id"], $data["cantidad"])) {
            echo json_encode($reservaservicio->crearReservaServicio(
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"]
            ));
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["id"], $data["reserva_id"], $data["servicio_id"], $data["cantidad"])) {
            echo json_encode($reservaservicio->actualizarReservaServicio(
                $data["id"],
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"]
            ));
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"] ?? null;
        echo json_encode($id ? $reservaservicio->eliminarReservaServicio($id) : ["error" => "Falta el parámetro ID"]);
        break;

    // 🧾 Nuevo caso para obtener la factura completa de una reserva
    case 'factura':
        $reserva_id = $_GET["reserva_id"] ?? null;
        echo json_encode($reserva_id ? 
            $reservaservicio->obtenerFacturaCompleta($reserva_id) : 
            ["error" => "Falta el parámetro reserva_id"]
        );
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
