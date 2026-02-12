<?php
require_once __DIR__ . '/../database/conexion.php';

class Usuarios {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConnection();
    }

    // ===============================
    // Obtener todos los usuarios
    // ===============================
    public function obtenerUsuarios() {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, correo, rol, creado_at FROM usuarios");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por ID
    public function obtenerUsuarioId($id) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, correo, rol, creado_at FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por nombre de usuario (para login)
    public function obtenerUsuarioPorUsuario($usuario) {
        $stmt = $this->conn->prepare("SELECT id, usuario, password, nombre, correo, rol, creado_at FROM usuarios WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===============================
    // Insertar usuario con hash y validación
    // ===============================
    public function insertarUsuario($usuario, $password, $nombre, $correo, $rol) {
        try {
            // Validar roles
            $roles_validos = ["admin", "recepcionista"];
            if (!in_array($rol, $roles_validos)) {
                return ["error" => "Rol inválido"];
            }

            // Validar duplicados
            $check = $this->conn->prepare("SELECT id FROM usuarios WHERE usuario = :usuario OR correo = :correo");
            $check->bindParam(':usuario', $usuario);
            $check->bindParam(':correo', $correo);
            $check->execute();

            if ($check->fetch()) {
                return ["error" => "El usuario o correo ya está registrado."];
            }

            // Hash de contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar
            $stmt = $this->conn->prepare("
                INSERT INTO usuarios (usuario, password, nombre, correo, rol, creado_at)
                VALUES (:usuario, :password, :nombre, :correo, :rol, NOW())
            ");

            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':rol', $rol);

            $stmt->execute();

            return ["success" => true, "mensaje" => "Usuario creado correctamente."];

        } catch (PDOException $e) {
            return ["error" => "Error al insertar: " . $e->getMessage()];
        }
    }

    // ===============================
    // Actualizar usuario
    // ===============================
    public function actualizarUsuario($id, $usuario, $password, $nombre, $correo, $rol) {
        try {
            // Validar roles
            $roles_validos = ["admin", "recepcionista"];
            if ($rol !== null && !in_array($rol, $roles_validos)) {
                return ["error" => "Rol inválido"];
            }

            // Validar duplicados (usuario o correo de otro registro)
            $check = $this->conn->prepare("SELECT id FROM usuarios WHERE (usuario = :usuario OR correo = :correo) AND id != :id");
            $check->bindParam(':usuario', $usuario);
            $check->bindParam(':correo', $correo);
            $check->bindParam(':id', $id);
            $check->execute();

            if ($check->fetch()) {
                return ["error" => "El usuario o correo ya está registrado por otro usuario."];
            }

            // Actualizar con o sin password
            if ($password === "" || $password === null) {
                $stmt = $this->conn->prepare("
                    UPDATE usuarios SET 
                        usuario = :usuario,
                        nombre = :nombre,
                        correo = :correo,
                        rol = :rol
                    WHERE id = :id
                ");

                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':rol', $rol);
                $stmt->bindParam(':id', $id);

            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("
                    UPDATE usuarios SET 
                        usuario = :usuario,
                        password = :password,
                        nombre = :nombre,
                        correo = :correo,
                        rol = :rol
                    WHERE id = :id
                ");

                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':password', $passwordHash);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':rol', $rol);
                $stmt->bindParam(':id', $id);
            }

            $stmt->execute();
            return ["success" => true, "mensaje" => "Usuario actualizado correctamente."];

        } catch (PDOException $e) {
            return ["error" => "Error al actualizar: " . $e->getMessage()];
        }
    }

    // ===============================
    // Eliminar usuario
    // ===============================
    public function eliminarUsuario($id) {
        try {
            global $CURRENT_USER;
            if ($id == $CURRENT_USER->id) {
                return ["error" => "No puedes eliminar tu propio usuario."];
            }

            $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return ["success" => true, "mensaje" => "Usuario eliminado correctamente."];

        } catch (PDOException $e) {
            return ["error" => "Error al eliminar: " . $e->getMessage()];
        }
    }
}
?>
