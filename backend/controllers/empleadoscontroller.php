<?php
require_once "../models/empleados.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $empleados = new Empleados();
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
            api_success("Empleados obtenidos", $empleados->obtenerEmpleados());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $empleados->obtenerEmpleado($id);
            if (!$item) {
                api_error("Empleado no encontrado", 404);
            }
            api_success("Empleado obtenido", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["nombre", "documento", "cargo", "correo", "telefono", "horario"]);
            $resultado = $empleados->crearEmpleado(
                $data["nombre"],
                $data["documento"],
                $data["cargo"],
                $data["correo"],
                $data["telefono"],
                $data["horario"]
            );
            api_success("Empleado creado correctamente", ["success" => (bool)$resultado], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "nombre", "documento", "cargo", "correo", "telefono", "horario"]);
            $resultado = $empleados->actualizarEmpleado(
                $data["id"],
                $data["nombre"],
                $data["documento"],
                $data["cargo"],
                $data["correo"],
                $data["telefono"],
                $data["horario"]
            );
            api_success("Empleado actualizado correctamente", ["success" => (bool)$resultado]);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $resultado = $empleados->eliminarEmpleado($id);
            api_success("Empleado eliminado correctamente", ["success" => (bool)$resultado]);
    }
});

