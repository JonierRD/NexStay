<?php
require_once "../models/facturas.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $facturas = new Facturas();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
        case 'crear':
        case 'generar_reserva':
        case 'generar_parqueadero':
            require_auth(['admin','recepcionista']);
            break;
        case 'actualizar':
        case 'eliminar':
            require_auth(['admin']);
            break;
        default:
            api_error("Operación no válida", 400);
    }

    $CURRENT_USER = getAuthUser();

    switch ($op) {
        case 'obtener':
            api_success("Facturas obtenidas", $facturas->obtenerFacturas());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $facturas->obtenerFactura((int)$id);
            if (!$item) {
                api_error("Factura no encontrada", 404);
            }
            api_success("Factura obtenida", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["monto", "metodo_pago"]);

            if (!empty($data["reserva_id"]) && !api_entity_exists($conexion, "reservas", $data["reserva_id"])) {
                api_error("El reserva_id no existe", 409);
            }
            if (!empty($data["parqueadero_id"]) && !api_entity_exists($conexion, "parqueadero", $data["parqueadero_id"])) {
                api_error("El parqueadero_id no existe", 409);
            }

            $resultado = $facturas->crearFactura(
                $data["reserva_id"] ?? null,
                $data["parqueadero_id"] ?? null,
                $data["monto"],
                $data["metodo_pago"],
                $data["fecha"] ?? null,
                $data["notas"] ?? "",
                $data["hospedaje_id"] ?? null
            );

            if (is_array($resultado) && isset($resultado["error"])) {
                api_error($resultado["error"], 500);
            }
            api_success("Factura creada correctamente", ["success" => true], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "monto", "metodo_pago"]);

            if (!api_entity_exists($conexion, "facturas", $data["id"])) {
                api_error("La factura no existe", 404);
            }
            if (!empty($data["reserva_id"]) && !api_entity_exists($conexion, "reservas", $data["reserva_id"])) {
                api_error("El reserva_id no existe", 409);
            }
            if (!empty($data["parqueadero_id"]) && !api_entity_exists($conexion, "parqueadero", $data["parqueadero_id"])) {
                api_error("El parqueadero_id no existe", 409);
            }

            $resultado = $facturas->actualizarFactura(
                $data["id"],
                $data["reserva_id"] ?? null,
                $data["parqueadero_id"] ?? null,
                $data["monto"],
                $data["metodo_pago"],
                $data["fecha"] ?? null,
                $data["notas"] ?? ""
            );
            if (is_array($resultado) && isset($resultado["error"])) {
                api_error($resultado["error"], 500);
            }
            api_success("Factura actualizada correctamente", ["success" => true]);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            if (!api_entity_exists($conexion, "facturas", $id)) {
                api_error("La factura no existe", 404);
            }

            $resultado = $facturas->eliminarFactura($id);
            if (is_array($resultado) && isset($resultado["error"])) {
                api_error($resultado["error"], 500);
            }
            api_success("Factura eliminada correctamente", ["success" => true]);

        case 'generar_reserva':
            $data = api_input_json();
            api_require_fields($data, ["reserva_id"]);

            if (!api_entity_exists($conexion, "reservas", $data["reserva_id"])) {
                api_error("El reserva_id no existe", 409);
            }

            $resultado = $facturas->generarFacturaPorReserva(
                $data["reserva_id"],
                $data["metodo_pago"] ?? "efectivo",
                $data["notas"] ?? ""
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Factura de reserva generada exitosamente", $resultado, 201);

        case 'generar_parqueadero':
            $data = api_input_json();
            api_require_fields($data, ["parqueadero_id"]);

            if (!api_entity_exists($conexion, "parqueadero", $data["parqueadero_id"])) {
                api_error("El parqueadero_id no existe", 409);
            }

            $resultado = $facturas->generarFacturaPorParqueadero(
                $data["parqueadero_id"],
                $data["metodo_pago"] ?? "efectivo",
                $data["notas"] ?? ""
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Factura de parqueadero generada exitosamente", $resultado, 201);
    }
});

