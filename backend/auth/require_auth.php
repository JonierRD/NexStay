<?php
require_once __DIR__ . "/JWT.php";
$config = include __DIR__ . "/config.php";

$jwt = new JWT($config["secret"], $config["token_exp"]);

/**
 * Obtiene token desde cookie o headers HTTP
 */
function get_token() {
    // 1️⃣ Buscar en cookie
    if (isset($_COOKIE["token"]) && !empty($_COOKIE["token"])) {
        return $_COOKIE["token"];
    }

    // 2️⃣ Buscar en header Authorization
    $authHeader = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? null;

    // 3️⃣ Intento extra con apache_request_headers()
    if (!$authHeader && function_exists("apache_request_headers")) {
        $headers = apache_request_headers();
        if (!empty($headers["Authorization"])) {
            $authHeader = $headers["Authorization"];
        }
    }

    // 4️⃣ Validar formato Bearer
    if ($authHeader && str_starts_with($authHeader, "Bearer ")) {
        return substr($authHeader, 7);
    }

    return null;
}

/**
 * Requiere token válido y opcionalmente rol permitido
 */
function require_auth($roles = []) {
    global $jwt;

    $token = get_token();

    if (!$token) {
        response_error("Token no proporcionado", 401);
    }

    try {
        // ANTES: JWT::verificarToken() ❌  
        // AHORA: $jwt->decode() ✔
        $decoded = $jwt->decode($token);
    } catch (Exception $e) {
        response_error("Token inválido: " . $e->getMessage(), 401);
    }

    // Convertir a objeto
    $user = (object) $decoded;

    // Validar roles
    if (!empty($roles) && !in_array($user->rol, $roles)) {
        response_error("Permiso denegado para este rol", 403);
    }

    // Guardar usuario global
    $GLOBALS["CURRENT_USER"] = $user;
}

/**
 * Devuelve usuario autenticado
 */
function getAuthUser() {
    return $GLOBALS["CURRENT_USER"] ?? null;
}

/**
 * Respuesta estándar de error
 */
function response_error($msg, $code) {
    http_response_code($code);
    echo json_encode([
        "success" => false,
        "error" => true,
        "message" => $msg,
        "data" => null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
