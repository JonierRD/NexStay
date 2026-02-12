<?php
require_once "../models/hospedajes.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $hospedajes = new Hospedajes();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
        case 'crear':
        case 'finalizar':
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
            api_success("Hospedajes obtenidos", $hospedajes->obtenerHospedajes());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $hospedajes->obtenerHospedaje($id);
            if (!$item) {
                api_error("Hospedaje no encontrado", 404);
            }
            api_success("Hospedaje obtenido", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["cliente_id", "habitacion_id", "fecha_entrada"]);

            if (!api_entity_exists($conexion, "clientes", $data["cliente_id"])) {
                api_error("El cliente_id no existe", 409);
            }
            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }

            $resultado = $hospedajes->crearHospedaje(
                $data["cliente_id"],
                $data["habitacion_id"],
                $data["fecha_entrada"]
            );

            if (!$resultado) {
                api_error("No se pudo crear el hospedaje", 400);
            }

            api_success("Hospedaje creado correctamente", ["success" => true], 201);

        case 'finalizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "fecha_salida"]);

            if (!api_entity_exists($conexion, "hospedajes", $data["id"])) {
                api_error("El hospedaje no existe", 404);
            }

            $resultado = $hospedajes->finalizarHospedaje($data["id"], $data["fecha_salida"]);
            if (!$resultado) {
                api_error("No se pudo finalizar el hospedaje", 400);
            }

            api_success("Hospedaje finalizado correctamente", ["success" => true]);

        case 'eliminar':
            $data = api_input_json();
            api_require_fields($data, ["id"]);

            if (!api_entity_exists($conexion, "hospedajes", $data["id"])) {
                api_error("El hospedaje no existe", 404);
            }

            $resultado = $hospedajes->eliminarHospedaje($data["id"]);
            if (!$resultado) {
                api_error("No se pudo eliminar el hospedaje", 400);
            }

            api_success("Hospedaje eliminado correctamente", ["success" => true]);
    }
});

