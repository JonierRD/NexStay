<?php
require_once "../models/Empleados.php";

header("Content-Type: application/json");

$empleados = new Empleados();

// Determinar la acción según el parámetro 'op' recibido por GET
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    // Obtener todos los empleados
    case 'obtener':
        $datos = $empleados->obtenerEmpleados();
        echo json_encode($datos);
        break;

    // Obtener un empleado por ID
    case 'obtener_id':
        $id = $_GET["id"];
        $datos = $empleados->obtenerEmpleado($id);
        echo json_encode($datos);
        break;

    // Crear nuevo empleado
    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["nombre"], $data["documento"], $data["cargo"], $data["correo"], $data["telefono"], $data["horario"])) {
            $resultado = $empleados->crearEmpleado(
                $data["nombre"],
                $data["documento"],
                $data["cargo"],
                $data["correo"],
                $data["telefono"],
                $data["horario"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    // Actualizar empleado
    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["id"], $data["nombre"], $data["documento"], $data["cargo"], $data["correo"], $data["telefono"], $data["horario"])) {
            $resultado = $empleados->actualizarEmpleado(
                $data["id"],
                $data["nombre"],
                $data["documento"],
                $data["cargo"],
                $data["correo"],
                $data["telefono"],
                $data["horario"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    // Eliminar empleado
    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $empleados->eliminarEmpleado($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
