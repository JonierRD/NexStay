<?php
require_once(__DIR__ . '/../database/conexion.php');

class Inventario {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // 🔹 Obtener todos los registros del inventario
    public function obtenerInventario() {
        $query = "SELECT * FROM inventario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Crear un nuevo registro en inventario
    public function crearItem($categoria, $nombre, $cantidad, $precio_unitario, $stock_minimo, $dias_mes, $ventas_mes, $total_existencia, $unidad) {
        $query = "INSERT INTO inventario 
        (categoria, nombre, cantidad, precio_unitario, stock_minimo, dias_mes, ventas_mes, total_existencia, unidad, actualizado_at)
        VALUES
        (:categoria, :nombre, :cantidad, :precio_unitario, :stock_minimo, :dias_mes, :ventas_mes, :total_existencia, :unidad, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio_unitario', $precio_unitario);
        $stmt->bindParam(':stock_minimo', $stock_minimo);
        $stmt->bindParam(':dias_mes', $dias_mes);
        $stmt->bindParam(':ventas_mes', $ventas_mes);
        $stmt->bindParam(':total_existencia', $total_existencia);
        $stmt->bindParam(':unidad', $unidad);
        return $stmt->execute();
    }

    // 🔹 Actualizar un registro existente
    public function actualizarItem($id, $categoria, $nombre, $cantidad, $precio_unitario, $stock_minimo, $dias_mes, $ventas_mes, $total_existencia, $unidad) {
        $query = "UPDATE inventario SET 
                    categoria = :categoria,
                    nombre = :nombre,
                    cantidad = :cantidad,
                    precio_unitario = :precio_unitario,
                    stock_minimo = :stock_minimo,
                    dias_mes = :dias_mes,
                    ventas_mes = :ventas_mes,
                    total_existencia = :total_existencia,
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
