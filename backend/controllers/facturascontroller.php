<?php
require_once "../models/Facturas.php";

header("Content-Type: application/json; charset=UTF-8");

$facturas = new Facturas();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

function responder($data, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($op) {

    // 🧾 Obtener todas las facturas
    case 'obtener':
        $datos = $facturas->obtenerFacturas();
        responder($datos);
        break;

    // 🧾 Obtener una factura por ID
    case 'obtener_id':
        if (empty($_GET["id"])) responder(["error" => "Falta el parámetro 'id'"], 400);
        $id = intval($_GET["id"]);
        $datos = $facturas->obtenerFactura($id);
        responder($datos ?: ["error" => "Factura no encontrada"], $datos ? 200 : 404);
        break;

    // ➕ Crear factura manual
    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["reserva_id"], $data["monto"], $data["metodo_pago"])) {
            responder(["error" => "Datos incompletos"], 400);
        }

        $resultado = $facturas->crearFactura(
            $data["reserva_id"],
            $data["monto"],
            $data["metodo_pago"],
            $data["fecha"] ?? null,
            $data["notas"] ?? ""
        );

        if (is_array($resultado) && isset($resultado["error"])) {
            responder(["error" => $resultado["error"]], 500);
        }
        responder(["success" => true, "mensaje" => "Factura creada correctamente"]);
        break;

    // ✏️ Actualizar factura
    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["id"], $data["reserva_id"], $data["monto"], $data["metodo_pago"])) {
            responder(["error" => "Datos incompletos"], 400);
        }

        $resultado = $facturas->actualizarFactura(
            $data["id"],
            $data["reserva_id"],
            $data["monto"],
            $data["metodo_pago"],
            $data["fecha"] ?? null,
            $data["notas"] ?? ""
        );

        if (is_array($resultado) && isset($resultado["error"])) {
            responder(["error" => $resultado["error"]], 500);
        }
        responder(["success" => true, "mensaje" => "Factura actualizada correctamente"]);
        break;

    // ❌ Eliminar factura
    case 'eliminar':
        if (empty($_GET["id"])) responder(["error" => "Falta el parámetro 'id'"], 400);
        $id = intval($_GET["id"]);
        $resultado = $facturas->eliminarFactura($id);

        if (is_array($resultado) && isset($resultado["error"])) {
            responder(["error" => $resultado["error"]], 500);
        }
        responder(["success" => true, "mensaje" => "Factura eliminada correctamente"]);
        break;

    // ⚙️ Generar factura automática por reserva
    case 'generar':
        $data = json_decode(file_get_contents("php://input"), true);
        $reserva_id = $data["reserva_id"] ?? null;

        if (!$reserva_id) responder(["error" => "Falta el parámetro 'reserva_id'"], 400);

        $resultado = $facturas->generarFacturaPorReserva(
            $reserva_id,
            $data["metodo_pago"] ?? "efectivo",
            $data["notas"] ?? ""
        );

        if (isset($resultado["error"])) {
            responder(["error" => $resultado["error"]], 400);
        }
        responder([
            "success" => true,
            "mensaje" => "Factura generada exitosamente",
            "detalle" => $resultado
        ]);
        break;

    // 📊 Obtener resumen financiero completo
   case 'obtener_completa':
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $datos = $facturas->obtenerFacturaCompleta($id);
        echo json_encode($datos);
    } else {
        echo json_encode(["error" => "Falta el parámetro id"]);
    }
    break;

    // 🚫 Operación no válida
    default:
        responder(["error" => "Operación no válida"], 400);
        break;
}
?>
