<?php
require_once "../models/Parqueadero.php";

header("Content-Type: application/json");

$parqueadero = new Parqueadero();

$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch($op) {

    case "obtener":
        $datos = $parqueadero->obtenerParqueadero();
        echo json_encode($datos);
        break;

    case "obtenerId":
        $id = $_GET["id"];
        $dato = $parqueadero->obtenerParqueaderoId($id);
        echo json_encode($dato);
        break;

case "crear":
    // Leer JSON enviado en body
    $data = json_decode(file_get_contents("php://input"), true);

    $placa = $data['placa'] ?? null;
    $cliente_id = $data['cliente_id'] ?? null;
    $habitacion_id = $data['habitacion_id'] ?? null;
    $tarifa = $data['tarifa'] ?? null;
    $fecha_entrada = $data['fecha_entrada'] ?? null;
    $fecha_salida = $data['fecha_salida'] ?? null;

    $resultado = $parqueadero->insertarParqueadero($placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida);
    echo json_encode(["success" => $resultado]);
    break;


    case "actualizar":
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $placa = $data['placa'] ?? null;
        $cliente_id = $data['cliente_id'] ?? null;
        $habitacion_id = $_POST["habitacion_id"];
        $tarifa = $_POST["tarifa"];
        $fecha_entrada = $_POST["fecha_entrada"];
        $fecha_salida = $_POST["fecha_salida"];

        $resultado = $parqueadero->actualizarParqueadero($id, $placa, $cliente_id, $habitacion_id, $tarifa, $fecha_entrada, $fecha_salida);
        echo json_encode(["success" => $resultado]);
        break;

    case "eliminar":
        $id = $_POST["id"];
        $resultado = $parqueadero->eliminarParqueadero($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
}
?>
