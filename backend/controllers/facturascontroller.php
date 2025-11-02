<?php
require_once "../models/Facturas.php";

header("Content-Type: application/json");

$facturas = new Facturas();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    case 'obtener':
        $datos = $facturas->obtenerFacturas();
        echo json_encode($datos);
        break;

    case 'obtener_id':
        $id = $_GET["id"];
        $datos = $facturas->obtenerFactura($id);
        echo json_encode($datos);
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["reserva_id"], $data["monto"], $data["metodo_pago"], $data["fecha"], $data["notas"])) {
            $resultado = $facturas->crearFactura(
                $data["reserva_id"],
                $data["monto"],
                $data["metodo_pago"],
                $data["fecha"],
                $data["notas"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["id"], $data["reserva_id"], $data["monto"], $data["metodo_pago"], $data["fecha"], $data["notas"])) {
            $resultado = $facturas->actualizarFactura(
                $data["id"],
                $data["reserva_id"],
                $data["monto"],
                $data["metodo_pago"],
                $data["fecha"],
                $data["notas"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $facturas->eliminarFactura($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;

        case 'generar':
    $data = json_decode(file_get_contents("php://input"), true);
    $reserva_id = $data['reserva_id'] ?? null;
    $metodo_pago = $data['metodo_pago'] ?? "efectivo";
    $notas = $data['notas'] ?? "";
    $resultado = $facturas->generarFacturaPorReserva($reserva_id, $metodo_pago, $notas);
    echo json_encode(["success" => $resultado]);
    break;

case 'obtener_completa':
    $id = $_GET["id"];
    $datos = $facturas->obtenerFacturaCompleta($id);
    echo json_encode($datos);
    break;

    
}
?>
