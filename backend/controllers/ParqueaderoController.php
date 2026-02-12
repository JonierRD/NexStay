<?php
require_once "../models/parqueadero.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $parqueadero = new Parqueadero();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch($op) {
        case "obtener":
        case "obtenerId":
        case "facturar":
            require_auth(['admin','recepcionista']);
            break;
        case "crear":
        case "actualizar":
        case "eliminar":
            require_auth(['admin']);
            break;
        default:
            api_error("Operación no válida", 400);
    }

    $CURRENT_USER = getAuthUser();

    switch($op) {
        case "obtener":
            api_success("Parqueadero obtenido", $parqueadero->obtenerParqueadero());

        case "obtenerId":
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $parqueadero->obtenerParqueaderoId($id);
            if (!$item) {
                api_error("Registro de parqueadero no encontrado", 404);
            }
            api_success("Registro obtenido", $item);

        case "crear":
            $data = api_input_json();
            api_require_fields($data, ["placa", "tipo_vehiculo", "fecha_entrada", "tarifa"]);
            $resultado = $parqueadero->insertarParqueadero(
                $data['nombre_cliente'] ?? null,
                $data['placa'],
                $data['tipo_vehiculo'],
                $data['fecha_entrada'],
                $data['fecha_salida'] ?? null,
                $data['tarifa'],
                $data['observaciones'] ?? null
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro creado correctamente", $resultado, 201);

        case "facturar":
            $data = api_input_json();
            api_require_fields($data, ["parqueadero_id"]);
            if (!api_entity_exists($conexion, "parqueadero", $data["parqueadero_id"])) {
                api_error("El parqueadero_id no existe", 409);
            }
            $resultado = $parqueadero->generarFacturaParqueadero(
                $data["parqueadero_id"],
                $data["metodo_pago"] ?? "efectivo",
                $data["notas"] ?? "Factura parqueadero cliente externo"
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Factura de parqueadero generada", $resultado, 201);

        case "actualizar":
            $data = api_input_json();
            api_require_fields($data, ["id"]);
            if (!api_entity_exists($conexion, "parqueadero", $data["id"])) {
                api_error("El registro de parqueadero no existe", 404);
            }
            $resultado = $parqueadero->actualizarParqueadero(
                $data['id'],
                $data['nombre_cliente'] ?? null,
                $data['placa'] ?? null,
                $data['tipo_vehiculo'] ?? null,
                $data['fecha_entrada'] ?? null,
                $data['fecha_salida'] ?? null,
                $data['tarifa'] ?? 0,
                $data['observaciones'] ?? null
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro actualizado correctamente", $resultado);

        case "eliminar":
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            if (!api_entity_exists($conexion, "parqueadero", $id)) {
                api_error("El registro de parqueadero no existe", 404);
            }
            $resultado = $parqueadero->eliminarParqueadero($id);
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro eliminado correctamente", $resultado);
    }
});

