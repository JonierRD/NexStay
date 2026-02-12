<?php
require_once "../models/clientes.php";
require_once "../models/hospedajes.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $clientes = new Clientes();
    $hospedajes = new Hospedajes();
    $conexion = (new Conexion())->getConnection();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
        case 'obtener_id':
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
            api_success("Clientes obtenidos", $clientes->obtenerClientes());

        case 'obtener_id':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $clientes->obtenerCliente($id);
            if (!$item) {
                api_error("Cliente no encontrado", 404);
            }
            api_success("Cliente obtenido", $item);

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, [
                "nombre", "cedula", "ciudad_origen", "ciudad_destino",
                "profesion", "documento", "correo", "telefono", "habitacion_id", "direccion"
            ]);

            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }

            $cliente_id = $clientes->crearCliente(
                $data["nombre"], $data["cedula"], $data["ciudad_origen"], $data["ciudad_destino"],
                $data["profesion"], $data["documento"], $data["correo"], $data["telefono"],
                $data["habitacion_id"], $data["direccion"]
            );

            if ($cliente_id === false) {
                api_error("Error creando cliente", 500);
            }

            $fecha = date("Y-m-d H:i:s");
            $hospedajeCreado = $hospedajes->crearHospedaje(
                $cliente_id,
                $data["habitacion_id"],
                $fecha
            );

            if (!$hospedajeCreado) {
                api_error("Cliente creado, pero falló el hospedaje", 500, ["cliente_id" => $cliente_id]);
            }

            api_success("Cliente y hospedaje creados correctamente", [
                "cliente_id" => $cliente_id,
                "hospedaje_creado" => true
            ], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, [
                "id", "nombre", "cedula", "ciudad_origen", "ciudad_destino",
                "profesion", "documento", "correo", "telefono", "habitacion_id", "direccion"
            ]);

            if (!api_entity_exists($conexion, "clientes", $data["id"])) {
                api_error("El cliente no existe", 404);
            }
            if (!api_entity_exists($conexion, "habitaciones", $data["habitacion_id"])) {
                api_error("El habitacion_id no existe", 409);
            }

            $sqlOld = "SELECT habitacion_id FROM clientes WHERE id = ?";
            $oldRoom = $conexion->prepare($sqlOld);
            $oldRoom->execute([$data["id"]]);
            $oldRoom = $oldRoom->fetchColumn();

            $resultado = $clientes->actualizarCliente(
                $data["id"], $data["nombre"], $data["cedula"], $data["ciudad_origen"], $data["ciudad_destino"],
                $data["profesion"], $data["documento"], $data["correo"], $data["telefono"],
                $data["habitacion_id"], $data["direccion"]
            );

            if ($resultado && $oldRoom != $data["habitacion_id"]) {
                if (!empty($oldRoom)) {
                    $conexion->prepare("UPDATE habitaciones SET estado = 'disponible' WHERE id = ?")->execute([$oldRoom]);
                }
                $conexion->prepare("UPDATE habitaciones SET estado = 'ocupada' WHERE id = ?")->execute([$data["habitacion_id"]]);
            }

            api_success("Cliente actualizado correctamente", ["success" => (bool)$resultado]);

        case 'eliminar':
            $data = api_input_json();
            api_require_fields($data, ["id"]);
            $id = $data["id"];

            if (!api_entity_exists($conexion, "clientes", $id)) {
                api_error("El cliente no existe", 404);
            }

            $sqlOld = "SELECT habitacion_id FROM clientes WHERE id = ?";
            $oldRoom = $conexion->prepare($sqlOld);
            $oldRoom->execute([$id]);
            $oldRoom = $oldRoom->fetchColumn();

            $resultado = $clientes->eliminarCliente($id);

            if ($resultado && !empty($oldRoom)) {
                $conexion->prepare("UPDATE habitaciones SET estado = 'disponible' WHERE id = ?")->execute([$oldRoom]);
            }

            api_success("Cliente eliminado correctamente", ["success" => (bool)$resultado]);
    }
});

