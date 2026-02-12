<?php
// backend/auth/Auth.php
require_once __DIR__ . "/JWT.php";
require_once __DIR__ . "/../database/conexion.php";

class Auth {

    private $db;

    public function __construct() {
        $this->db = new Conexion();
        $this->db = $this->db->getConnection();
    }

    // =========================================================
    // LOGIN
    // =========================================================
    public function login($usuario, $password) {

        $stmt = $this->db->prepare("SELECT id, nombre, usuario, password, rol 
                                    FROM usuarios 
                                    WHERE usuario = :usuario");
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ["success" => false, "message" => "Usuario no encontrado"];
        }

        if (!password_verify($password, $user["password"])) {
            return ["success" => false, "message" => "Contraseña incorrecta"];
        }

        // Datos que guardará el token
        $data = [
            "id" => $user["id"],
            "nombre" => $user["nombre"],
            "rol" => $user["rol"]
        ];

        // Crear el token con la clase JWT
        $config = require __DIR__ . "/config.php";
        $jwt = new JWT($config['secret'], $config['token_exp']);
        $token = $jwt->encode($data);

        return [
            "success" => true, 
            "token" => $token,
            "usuario" => $user["usuario"],
            "nombre" => $user["nombre"],
            "rol" => $user["rol"]
        ];
    }

    // =========================================================
    // OBTENER USUARIO ACTUAL DESDE EL TOKEN EN COOKIE
    // =========================================================
    public function getCurrentUser() {

        if (!isset($_COOKIE["token"])) {
            return null;
        }

        $token = $_COOKIE["token"];

        $decoded = JWT::verificarToken($token);

        if (isset($decoded["error"])) {
            return null;
        }

        return $decoded->data;
    }

    // =========================================================
    // LOGOUT
    // =========================================================
    public function logout() {
        setcookie("token", "", time() - 3600, "/");
        return true;
    }
}
