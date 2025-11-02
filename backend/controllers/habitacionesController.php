<?php
require_once "../models/Habitaciones.php";

header("Content-Type: application/json");

$habitaciones = new Habitaciones();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch($op) {

    case 'obtener':
        echo json_encode($habitaciones->obtenerHabitaciones());
        break;

    case 'obtener_id':
        $id = $_GET["id"];
        echo json_encode($habitaciones->obtenerHabitacion($id));
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["numero"], $data["tipo"], $data["descripcion"], $data["precio"], $data["estado"])) {
            $resultado = $habitaciones->crearHabitacion(
                $data["numero"],
                $data["tipo"],
                $data["descripcion"],
                $data["precio"],
                $data["estado"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error"=>"Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["id"], $data["numero"], $data["tipo"], $data["descripcion"], $data["precio"], $data["estado"])) {
            $resultado = $habitaciones->actualizarHabitacion(
                $data["id"],
                $data["numero"],
                $data["tipo"],
                $data["descripcion"],
                $data["precio"],
                $data["estado"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error"=>"Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $habitaciones->eliminarHabitacion($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error"=>"Operación no válida"]);
        break;
}
?>
