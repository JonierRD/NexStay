<?php
require_once "../models/usuarios.php";
require_once "../auth/require_auth.php";
require_once __DIR__ . "/../api/response.php";

api_guard(function () {
    $usuarios = new Usuarios();
    $op = $_GET["op"] ?? "";

    require_auth(['admin']);
    $CURRENT_USER = getAuthUser();

    switch($op) {
        case "obtener":
            api_success("Usuarios obtenidos", $usuarios->obtenerUsuarios());

        case "obtenerId":
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $item = $usuarios->obtenerUsuarioId($id);
            if (!$item) {
                api_error("Usuario no encontrado", 404);
            }
            api_success("Usuario obtenido", $item);

        case "insertar":
            $data = api_input_json();
            api_require_fields($data, ["usuario", "password", "rol"]);
            $resultado = $usuarios->insertarUsuario(
                $data["usuario"],
                $data["password"],
                $data["nombre"] ?? null,
                $data["correo"] ?? null,
                $data["rol"]
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Usuario creado correctamente", $resultado, 201);

        case "actualizar":
            $data = api_input_json();
            api_require_fields($data, ["id"]);
            $resultado = $usuarios->actualizarUsuario(
                $data["id"],
                $data["usuario"] ?? null,
                $data["password"] ?? null,
                $data["nombre"] ?? null,
                $data["correo"] ?? null,
                $data["rol"] ?? null
            );
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Usuario actualizado correctamente", $resultado);

        case "eliminar":
            $id = $_GET["id"] ?? null;
            if (!$id) {
                api_error("Falta el parámetro id", 400);
            }
            $resultado = $usuarios->eliminarUsuario($id);
            if (isset($resultado["error"])) {
                api_error($resultado["error"], 400);
            }
            api_success("Usuario eliminado correctamente", $resultado);

        default:
            api_error("Operación no válida", 400);
    }
});

