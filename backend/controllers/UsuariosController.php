<?php
require_once "../models/Usuarios.php";

header("Content-Type: application/json");

$usuarios = new Usuarios();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch($op) {

    case "obtener":
        $datos = $usuarios->obtenerUsuarios();
        echo json_encode($datos);
        break;

    case "obtenerId":
        $id = $_GET["id"];
        $dato = $usuarios->obtenerUsuarioId($id);
        echo json_encode($dato);
        break;

    case "insertar":
        $data = json_decode(file_get_contents("php://input"), true);

        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $correo = $data['correo'] ?? null;
        $rol = $data['rol'] ?? null;

        $resultado = $usuarios->insertarUsuario($usuario, $password, $nombre, $correo, $rol);
        echo json_encode(["success" => $resultado]);
        break;

    case "actualizar":
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $correo = $data['correo'] ?? null;
        $rol = $data['rol'] ?? null;

        $resultado = $usuarios->actualizarUsuario($id, $usuario, $password, $nombre, $correo, $rol);
        echo json_encode(["success" => $resultado]);
        break;

    case "eliminar":
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        $resultado = $usuarios->eliminarUsuario($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
}
?>
