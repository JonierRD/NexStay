<?php
require_once "../models/servicios.php";
require_once "../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $servicios = new Servicios();
    $op = $_GET["op"] ?? "";

    switch($op) {
        case "obtener":
        case "obtenerId":
            require_auth(['admin','recepcionista']);
            break;
        case "insertar":
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
            api_success("Servicios obtenidos", $servicios->obtenerServicios());

        case "obtenerId":
            $id = $_GET["id"] ?? null;
            if(!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $servicios->obtenerServicioId($id);
            if (!$item) {
                api_error("Servicio no encontrado", 404);
            }
            api_success("Servicio obtenido", $item);

        case "insertar":
            $data = api_input_json();
            api_require_fields($data, ["nombre", "precio"]);
            $ok = $servicios->insertarServicio(
                $data['nombre'],
                $data['descripcion'] ?? null,
                $data['precio'],
                $data['disponible'] ?? 1
            );
            api_success("Servicio creado correctamente", ["success" => (bool)$ok], 201);

        case "actualizar":
            $data = api_input_json();
            api_require_fields($data, ["id"]);
            $ok = $servicios->actualizarServicio(
                $data['id'],
                $data['nombre'] ?? null,
                $data['descripcion'] ?? null,
                $data['precio'] ?? null,
                $data['disponible'] ?? null
            );
            api_success("Servicio actualizado correctamente", ["success" => (bool)$ok]);

        case "eliminar":
            $id = $_POST["id"] ?? null;
            if(!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $ok = $servicios->eliminarServicio($id);
            api_success("Servicio eliminado correctamente", ["success" => (bool)$ok]);
    }
});

