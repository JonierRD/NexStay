<?php
require_once "../models/reservas.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $reservas = new Reservas();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
            require_auth(['admin','recepcionista']);
            break;
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

    $CURRENT_USER = getAuthUser();

    switch ($op) {
        case 'obtener':
            api_success("Reservas obtenidas", $reservas->obtenerReservas());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $reservas->obtenerReserva($id);
            if (!$item) {
                api_error("Reserva no encontrada", 404);
            }
            api_success("Reserva obtenida", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["cliente_id", "habitacion_id", "fecha_entrada", "fecha_salida", "total", "estado"]);

            if (!api_entity_exists($conexion, "clientes", $data["cliente_id"])) {
                api_error("El cliente_id no existe", 409);
            }
            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }

            $resultado = $reservas->crearReserva(
                $data["cliente_id"],
                $data["habitacion_id"],
                $data["fecha_entrada"],
                $data["fecha_salida"],
                $data["total"],
                $data["estado"]
            );

            if (is_array($resultado) && isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Reserva creada correctamente", $resultado, 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "cliente_id", "habitacion_id", "fecha_entrada", "fecha_salida", "total", "estado"]);

            if (!api_entity_exists($conexion, "reservas", $data["id"])) {
                api_error("La reserva no existe", 404);
            }
            if (!api_entity_exists($conexion, "clientes", $data["cliente_id"])) {
                api_error("El cliente_id no existe", 409);
            }
            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }

            $resultado = $reservas->actualizarReserva(
                $data["id"],
                $data["cliente_id"],
                $data["habitacion_id"],
                $data["fecha_entrada"],
                $data["fecha_salida"],
                $data["total"],
                $data["estado"]
            );
            api_success("Reserva actualizada correctamente", $resultado);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            if (!api_entity_exists($conexion, "reservas", $id)) {
                api_error("La reserva no existe", 404);
            }
            $resultado = $reservas->eliminarReserva($id);
            if (is_array($resultado) && isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Reserva eliminada correctamente", $resultado);
    }
});
