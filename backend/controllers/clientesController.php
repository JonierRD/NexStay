<?php
require_once "../models/Clientes.php";

header("Content-Type: application/json");

$clientes = new Clientes();

// Determinar la acción según el parámetro 'op' recibido por GET
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    // Obtener todos los clientes
    case 'obtener':
        $datos = $clientes->obtenerClientes();
        echo json_encode($datos);
        break;

    // Obtener un cliente por ID
    case 'obtener_id':
        $id = $_GET["id"];
        $datos = $clientes->obtenerCliente($id);
        echo json_encode($datos);
        break;

    // Crear nuevo cliente
    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["nombre"], $data["cedula"], $data["ciudad_origen"], $data["ciudad_destino"], $data["profesion"], $data["documento"], $data["correo"], $data["telefono"], $data["habitacion_id"], $data["direccion"])) {
            $resultado = $clientes->crearCliente(
                $data["nombre"],
                $data["cedula"],
                $data["ciudad_origen"],
                $data["ciudad_destino"],
                $data["profesion"],
                $data["documento"],
                $data["correo"],
                $data["telefono"],
                $data["habitacion_id"],
                $data["direccion"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    // Actualizar cliente
    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["id"], $data["nombre"], $data["cedula"], $data["ciudad_origen"], $data["ciudad_destino"], $data["profesion"], $data["documento"], $data["correo"], $data["telefono"], $data["habitacion_id"], $data["direccion"])) {
            $resultado = $clientes->actualizarCliente(
                $data["id"],
                $data["nombre"],
                $data["cedula"],
                $data["ciudad_origen"],
                $data["ciudad_destino"],
                $data["profesion"],
                $data["documento"],
                $data["correo"],
                $data["telefono"],
                $data["habitacion_id"],
                $data["direccion"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    // Eliminar cliente
    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $clientes->eliminarCliente($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
