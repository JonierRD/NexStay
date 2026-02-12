<?php
require_once __DIR__ . '/../database/conexion.php';

class Clientes {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los clientes
    public function obtenerClientes() {
        $query = "SELECT * FROM clientes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
// Crear un nuevo cliente (modificado para devolver el ID insertado)
public function crearCliente($nombre, $cedula, $ciudad_origen, $ciudad_destino, $profesion, $documento, $correo, $telefono, $habitacion_id, $direccion) {
    $query = "INSERT INTO clientes 
        (nombre, cedula, ciudad_origen, ciudad_destino, profesion, documento, correo, telefono, habitacion_id, direccion, creado_at) 
        VALUES 
        (:nombre, :cedula, :ciudad_origen, :ciudad_destino, :profesion, :documento, :correo, :telefono, :habitacion_id, :direccion, NOW())";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':cedula', $cedula);
    $stmt->bindParam(':ciudad_origen', $ciudad_origen);
    $stmt->bindParam(':ciudad_destino', $ciudad_destino);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->bindParam(':documento', $documento);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':habitacion_id', $habitacion_id);
    $stmt->bindParam(':direccion', $direccion);

    if($stmt->execute()){
        // devolver el id insertado (string o int)
        return $this->conn->lastInsertId();
    } else {
        // Muestra error real de MySQL para depuración (puedes quitar en producción)
        // error_log(print_r($stmt->errorInfo(), true));
        return false;
    }
}


// Obtener un cliente por ID
public function obtenerCliente($id) {
    $query = "SELECT * FROM clientes WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() devuelve un solo registro
}


    // Actualizar cliente
    public function actualizarCliente($id, $nombre, $cedula, $ciudad_origen, $ciudad_destino, $profesion, $documento, $correo, $telefono, $habitacion_id, $direccion) {
        $query = "UPDATE clientes SET 
                    nombre = :nombre, 
                    cedula = :cedula,
                    ciudad_origen = :ciudad_origen,
                    ciudad_destino = :ciudad_destino,
                    profesion = :profesion,
                    documento = :documento,
                    correo = :correo,
                    telefono = :telefono,
                    habitacion_id = :habitacion_id,
                    direccion = :direccion
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->bindParam(':ciudad_origen', $ciudad_origen);
        $stmt->bindParam(':ciudad_destino', $ciudad_destino);
        $stmt->bindParam(':profesion', $profesion);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':habitacion_id', $habitacion_id);
        $stmt->bindParam(':direccion', $direccion);
        return $stmt->execute();
    }

    // Eliminar cliente
    public function eliminarCliente($id) {
        $query = "DELETE FROM clientes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
