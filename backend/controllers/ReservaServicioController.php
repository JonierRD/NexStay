<?php
require_once "../models/reservaservicio.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $reservaservicio = new ReservaServicio();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
        case 'factura':
        case 'crear':
        case 'actualizar':
            require_auth(['admin','recepcionista']);
            break;
        case 'eliminar':
            require_auth(['admin']);
            break;
        default:
            api_error("Operación no válida", 400);
    }

    switch ($op) {
        case 'obtener':
            api_success("Registros obtenidos", $reservaservicio->obtenerReservaServicios());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $reservaservicio->obtenerReservaServicio($id);
            if (!$item) {
                api_error("Registro no encontrado", 404);
            }
            api_success("Registro obtenido", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["reserva_id", "servicio_id", "cantidad"]);

            if (!api_entity_exists($conexion, "reservas", $data["reserva_id"])) {
                api_error("El reserva_id no existe", 409);
            }
            if (!api_entity_exists($conexion, "servicios", $data["servicio_id"])) {
                api_error("El servicio_id no existe", 409);
            }

            $resultado = $reservaservicio->crearReservaServicio(
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"]
            );

            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro creado correctamente", $resultado, 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "reserva_id", "servicio_id", "cantidad"]);

            if (!api_entity_exists($conexion, "reserva_servicio", $data["id"])) {
                api_error("El registro no existe", 404);
            }
            if (!api_entity_exists($conexion, "reservas", $data["reserva_id"])) {
                api_error("El reserva_id no existe", 409);
            }
            if (!api_entity_exists($conexion, "servicios", $data["servicio_id"])) {
                api_error("El servicio_id no existe", 409);
            }

            $resultado = $reservaservicio->actualizarReservaServicio(
                $data["id"],
                $data["reserva_id"],
                $data["servicio_id"],
                $data["cantidad"]
            );

            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro actualizado correctamente", $resultado);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            if (!api_entity_exists($conexion, "reserva_servicio", $id)) {
                api_error("El registro no existe", 404);
            }

            $resultado = $reservaservicio->eliminarReservaServicio($id);
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Registro eliminado correctamente", $resultado);

        case 'factura':
            $reserva_id = $_GET["reserva_id"] ?? null;
            if (!$reserva_id) {
                api_error("Falta el parámetro reserva_id", 400);
            }
            if (!api_entity_exists($conexion, "reservas", $reserva_id)) {
                api_error("La reserva no existe", 404);
            }

            $detalle = $reservaservicio->obtenerFacturaCompleta($reserva_id);
            if (isset($detalle["error"])) {
                api_error($detalle["error"], 400);
            }
            api_success("Factura de reserva obtenida", $detalle);
    }
});

