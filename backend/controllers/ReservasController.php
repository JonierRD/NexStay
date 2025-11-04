<?php
require_once "../models/Reservas.php";

header("Content-Type: application/json");

$reservas = new Reservas();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    case 'obtener':
        echo json_encode($reservas->obtenerReservas());
        break;

    case 'obtener_id':
        $id = $_GET["id"];
        echo json_encode($reservas->obtenerReserva($id));
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["cliente_id"], $data["habitacion_id"], $data["fecha_entrada"], $data["fecha_salida"], $data["total"], $data["estado"])) {
            $resultado = $reservas->crearReserva(
                $data["cliente_id"],
                $data["habitacion_id"],
                $data["fecha_entrada"],
                $data["fecha_salida"],
                $data["total"],
                $data["estado"]
            );
            echo json_encode($resultado);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["id"], $data["cliente_id"], $data["habitacion_id"], $data["fecha_entrada"], $data["fecha_salida"], $data["total"], $data["estado"])) {
            $resultado = $reservas->actualizarReserva(
                $data["id"],
                $data["cliente_id"],
                $data["habitacion_id"],
                $data["fecha_entrada"],
                $data["fecha_salida"],
                $data["total"],
                $data["estado"]
            );
            echo json_encode($resultado);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $reservas->eliminarReserva($id);
        echo json_encode($resultado);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
