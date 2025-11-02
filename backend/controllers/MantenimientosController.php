<?php
require_once "../models/Mantenimientos.php";

header("Content-Type: application/json");

$mantenimientos = new Mantenimientos();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch($op) {

    case 'obtener':
        echo json_encode($mantenimientos->obtenerMantenimientos());
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["habitacion_id"], $data["descripcion"], $data["estado"], $data["fecha_reporte"], $data["fecha_solucion"], $data["empleado_id"])) {
            $resultado = $mantenimientos->crearMantenimiento(
                $data["habitacion_id"],
                $data["descripcion"],
                $data["estado"],
                $data["fecha_reporte"],
                $data["fecha_solucion"],
                $data["empleado_id"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error"=>"Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["id"], $data["habitacion_id"], $data["descripcion"], $data["estado"], $data["fecha_reporte"], $data["fecha_solucion"], $data["empleado_id"])) {
            $resultado = $mantenimientos->actualizarMantenimiento(
                $data["id"],
                $data["habitacion_id"],
                $data["descripcion"],
                $data["estado"],
                $data["fecha_reporte"],
                $data["fecha_solucion"],
                $data["empleado_id"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error"=>"Datos incompletos"]);
        }
        break;

    case 'obtener_id':
    $id = $_GET["id"];
    echo json_encode($mantenimientos->obtenerMantenimiento($id));
    break;


    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $mantenimientos->eliminarMantenimiento($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error"=>"Operación no válida"]);
        break;
}
?>
