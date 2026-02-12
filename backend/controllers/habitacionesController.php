<?php
require_once "../models/habitaciones.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $habitaciones = new Habitaciones();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
            require_auth(['admin','recepcionista']);
            break;
        case 'crear':
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
            api_success("Habitaciones obtenidas", $habitaciones->obtenerHabitaciones());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $habitaciones->obtenerHabitacion($id);
            if (!$item) {
                api_error("Habitación no encontrada", 404);
            }
            api_success("Habitación obtenida", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["numero", "tipo", "descripcion", "precio", "estado"]);
            $resultado = $habitaciones->crearHabitacion(
                $data["numero"],
                $data["tipo"],
                $data["descripcion"],
                $data["precio"],
                $data["estado"]
            );
            api_success("Habitación creada correctamente", ["success" => (bool)$resultado], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "numero", "tipo", "descripcion", "precio", "estado"]);
            $resultado = $habitaciones->actualizarHabitacion(
                $data["id"],
                $data["numero"],
                $data["tipo"],
                $data["descripcion"],
                $data["precio"],
                $data["estado"]
            );
            api_success("Habitación actualizada correctamente", ["success" => (bool)$resultado]);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $resultado = $habitaciones->eliminarHabitacion($id);
            api_success("Habitación eliminada correctamente", ["success" => (bool)$resultado]);
    }
});

