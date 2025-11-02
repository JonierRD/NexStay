<?php
require_once "../models/ReservaServicio.php";

header("Content-Type: application/json");

$reservaservicio = new ReservaServicio();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    case 'obtener':
        echo json_encode($reservaservicio->obtenerReservaServicios());
        break;

    case 'obtener_id':
        $id = $_GET["id"];
        echo json_encode($reservaservicio->obtenerReservaServicio($id));
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["reserva_id"], $data["servicio_id"], $data["cantidad"], $data["total"])) {
            $resultado = $reservaservicio->crearReservaServicio(
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"],
                $data["total"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["id"], $data["reserva_id"], $data["servicio_id"], $data["cantidad"], $data["total"])) {
            $resultado = $reservaservicio->actualizarReservaServicio(
                $data["id"],
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"],
                $data["total"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $reservaservicio->eliminarReservaServicio($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
