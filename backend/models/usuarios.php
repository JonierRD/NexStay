<?php
require_once __DIR__ . '/../database/conexion.php';

class Usuarios {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios() {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por ID
    public function obtenerUsuarioId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insertar usuario
    public function insertarUsuario($usuario, $password, $nombre, $correo, $rol) {
        $stmt = $this->conn->prepare("
            INSERT INTO usuarios (usuario, password, nombre, correo, rol, creado_at)
            VALUES (:usuario, :password, :nombre, :correo, :rol, NOW())
        ");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Actualizar usuario
    public function actualizarUsuario($id, $usuario, $password, $nombre, $correo, $rol) {
        $stmt = $this->conn->prepare("
            UPDATE usuarios SET 
                usuario = :usuario,
                password = :password,
                nombre = :nombre,
                correo = :correo,
                rol = :rol
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Eliminar usuario
    public function eliminarUsuario($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
