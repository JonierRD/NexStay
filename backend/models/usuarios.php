<?php
require_once '../database/conexion.php';

class Usuarios {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios() {
        $sql = "SELECT * FROM usuarios";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por ID
    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario
    public function crearUsuario($data) {
        $sql = "INSERT INTO usuarios (usuario, password, nombre, correo, rol, creado_at)
                VALUES (?, ?, ?, ?, ?, ?)";
        $consulta = $this->conexion->prepare($sql);

        // Encriptar contraseña antes de guardar
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        return $consulta->execute([
            $data['usuario'],
            $passwordHash,
            $data['nombre'],
            $data['correo'],
            $data['rol'],
            $data['creado_at']
        ]);
    }

    // Actualizar un usuario existente
    public function actualizarUsuario($id, $data) {
        // Si se envía una nueva contraseña, la encripta; si no, conserva la actual
        if (!empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET 
                        usuario = ?, 
                        password = ?, 
                        nombre = ?, 
                        correo = ?, 
                        rol = ? 
                    WHERE id = ?";
            $params = [
                $data['usuario'],
                $passwordHash,
                $data['nombre'],
                $data['correo'],
                $data['rol'],
                $id
            ];
        } else {
            $sql = "UPDATE usuarios SET 
                        usuario = ?, 
                        nombre = ?, 
                        correo = ?, 
                        rol = ? 
                    WHERE id = ?";
            $params = [
                $data['usuario'],
                $data['nombre'],
                $data['correo'],
                $data['rol'],
                $id
            ];
        }

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute($params);
    }

    // Eliminar un usuario
    public function eliminarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$id]);
    }

    // Verificar login (usuario y contraseña)
    public function verificarCredenciales($usuario, $password) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$usuario]);
        $user = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
