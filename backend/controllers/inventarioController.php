<?php
require_once "../models/inventario.php";
require_once "../database/conexion.php";
require_once __DIR__ . "/../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $inventario = new Inventario();
    $op = $_GET["op"] ?? "";

    switch ($op) {
        case 'obtener':
            require_auth(['admin', 'recepcionista']);
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
            api_success("Inventario obtenido", $inventario->obtenerInventario());

        case 'crear':
            $data = api_input_json();
            api_require_fields($data, ["categoria", "nombre", "cantidad", "precio_unitario", "stock_minimo", "dias_mes", "ventas_mes", "unidad"]);
            $resultado = $inventario->crearItem(
                $data["categoria"],
                $data["nombre"],
                $data["cantidad"],
                $data["precio_unitario"],
                $data["stock_minimo"],
                $data["dias_mes"],
                $data["ventas_mes"],
                $data["unidad"]
            );
            api_success("Item creado correctamente", ["success" => (bool)$resultado], 201);

        case 'actualizar':
            $data = api_input_json();
            api_require_fields($data, ["id", "categoria", "nombre", "cantidad", "precio_unitario", "stock_minimo", "dias_mes", "ventas_mes", "unidad"]);
            $resultado = $inventario->actualizarItem(
                $data["id"],
                $data["categoria"],
                $data["nombre"],
                $data["cantidad"],
                $data["precio_unitario"],
                $data["stock_minimo"],
                $data["dias_mes"],
                $data["ventas_mes"],
                $data["unidad"]
            );
            api_success("Item actualizado correctamente", ["success" => (bool)$resultado]);

        case 'eliminar':
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $resultado = $inventario->eliminarItem($id);
            api_success("Item eliminado correctamente", ["success" => (bool)$resultado]);
    }
});

