<?php
require_once __DIR__ . '/../database/conexion.php';

class Inventario {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los items
    public function obtenerInventario() {
        $query = "SELECT * FROM inventario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear item
    public function crearItem($categoria, $nombre, $cantidad, $precio_unitario, $stock_minimo, $dias_mes, $ventas_mes, $unidad) {
        $query = "INSERT INTO inventario 
        (categoria, nombre, cantidad, precio_unitario, stock_minimo, dias_mes, ventas_mes, unidad, actualizado_at)
        VALUES 
        (:categoria, :nombre, :cantidad, :precio_unitario, :stock_minimo, :dias_mes, :ventas_mes, :unidad, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio_unitario', $precio_unitario);
        $stmt->bindParam(':stock_minimo', $stock_minimo);
        $stmt->bindParam(':dias_mes', $dias_mes);
        $stmt->bindParam(':ventas_mes', $ventas_mes);
        $stmt->bindParam(':unidad', $unidad);

        return $stmt->execute();
    }

    // Actualizar item
    public function actualizarItem($id, $categoria, $nombre, $cantidad, $precio_unitario, $stock_minimo, $dias_mes, $ventas_mes, $unidad) {
        $query = "UPDATE inventario SET 
                    categoria = :categoria,
                    nombre = :nombre,
                    cantidad = :cantidad,
                    precio_unitario = :precio_unitario,
                    stock_minimo = :stock_minimo,
                    dias_mes = :dias_mes,
                    ventas_mes = :ventas_mes,
                    unidad = :unidad,
                    actualizado_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio_unitario', $precio_unitario);
        $stmt->bindParam(':stock_minimo', $stock_minimo);
        $stmt->bindParam(':dias_mes', $dias_mes);
        $stmt->bindParam(':ventas_mes', $ventas_mes);
        $stmt->bindParam(':unidad', $unidad);

        return $stmt->execute();
    }

    // Eliminar item
    public function eliminarItem($id) {
        $query = "DELETE FROM inventario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
