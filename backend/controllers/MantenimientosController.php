<?php
require_once "../models/mantenimientos.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $mantenimientos = new Mantenimientos();
    $conexion = (new Conexion())->getConnection();
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
            api_success("Mantenimientos obtenidos", $mantenimientos->obtenerMantenimientos());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $mantenimientos->obtenerMantenimiento($id);
            if (!$item) {
                api_error("Mantenimiento no encontrado", 404);
            }
            api_success("Mantenimiento obtenido", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["habitacion_id", "descripcion", "estado", "fecha_reporte", "fecha_solucion", "empleado_id"]);

            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }
            if (!empty($data["empleado_id"]) && !api_entity_exists($conexion, "empleados", $data["empleado_id"])) {
                api_error("El empleado_id no existe", 409);
            }

            $resultado = $mantenimientos->crearMantenimiento(
                $data["habitacion_id"],
                $data["descripcion"],
                $data["estado"],
                $data["fecha_reporte"],
                $data["fecha_solucion"],
                $data["empleado_id"]
            );
            api_success("Mantenimiento creado correctamente", ["success" => (bool)$resultado], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "habitacion_id", "descripcion", "estado", "fecha_reporte", "fecha_solucion", "empleado_id"]);

            if (!api_entity_exists($conexion, "mantenimientos", $data["id"])) {
                api_error("El mantenimiento no existe", 404);
            }
            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }
            if (!empty($data["empleado_id"]) && !api_entity_exists($conexion, "empleados", $data["empleado_id"])) {
                api_error("El empleado_id no existe", 409);
            }

            $resultado = $mantenimientos->actualizarMantenimiento(
                $data["id"],
                $data["habitacion_id"],
                $data["descripcion"],
                $data["estado"],
                $data["fecha_reporte"],
                $data["fecha_solucion"],
                $data["empleado_id"]
            );
            api_success("Mantenimiento actualizado correctamente", ["success" => (bool)$resultado]);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            if (!api_entity_exists($conexion, "mantenimientos", $id)) {
                api_error("El mantenimiento no existe", 404);
            }
            $resultado = $mantenimientos->eliminarMantenimiento($id);
            api_success("Mantenimiento eliminado correctamente", ["success" => (bool)$resultado]);
    }
});

