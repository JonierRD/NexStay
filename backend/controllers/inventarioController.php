<?php
require_once "../models/Inventario.php";

header("Content-Type: application/json");

$inventario = new Inventario();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case 'obtener':
        echo json_encode($inventario->obtenerInventario());
        break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["categoria"], $data["nombre"], $data["cantidad"], $data["precio_unitario"], $data["stock_minimo"], $data["dias_mes"], $data["ventas_mes"], $data["total_existencia"], $data["unidad"])) {
            $resultado = $inventario->crearItem(
                $data["categoria"],
                $data["nombre"],
                $data["cantidad"],
                $data["precio_unitario"],
                $data["stock_minimo"],
                $data["dias_mes"],
                $data["ventas_mes"],
                $data["total_existencia"],
                $data["unidad"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data["id"], $data["categoria"], $data["nombre"], $data["cantidad"], $data["precio_unitario"], $data["stock_minimo"], $data["dias_mes"], $data["ventas_mes"], $data["total_existencia"], $data["unidad"])) {
            $resultado = $inventario->actualizarItem(
                $data["id"],
                $data["categoria"],
                $data["nombre"],
                $data["cantidad"],
                $data["precio_unitario"],
                $data["stock_minimo"],
                $data["dias_mes"],
                $data["ventas_mes"],
                $data["total_existencia"],
                $data["unidad"]
            );
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'eliminar':
        $id = $_GET["id"];
        $resultado = $inventario->eliminarItem($id);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
