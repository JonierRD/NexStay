<?php
require_once "../models/Servicios.php";

header("Content-Type: application/json");

$servicios = new Servicios();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch($op) {

    case "obtener":
        $datos = $servicios->obtenerServicios();
        echo json_encode($datos);
        break;

    case "obtenerId":
        $id = $_GET["id"];
        $dato = $servicios->obtenerServicioId($id);
        echo json_encode($dato);
        break;

case "insertar":
    // Leer JSON enviado en body
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'] ?? null;
    $descripcion = $data['descripcion'] ?? null;
    $precio = $data['precio'] ?? null;
    $disponible = $data['disponible'] ?? null;

    $resultado = $servicios->insertarServicio($nombre, $descripcion, $precio, $disponible);
    echo json_encode(["success" => $resultado]);
    break;


    case "actualizar":
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $precio = $data['precio'] ?? null;
        $disponible = $data['disponible'] ?? null;

        $resultado = $servicios->actualizarServicio($id, $nombre, $descripcion, $precio, $disponible);
        echo json_encode(["success" => $resultado]);
        break;

    case "eliminar":
        $id = $_POST["id"];
        $resultado = $servicios->eliminarServicio($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
}
?>
