<?php
require_once __DIR__ . "/../api/response.php";
require_once __DIR__ . "/Auth.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

api_guard(function () {
    $auth = new Auth();
    $input = api_input_json();
    api_require_fields($input, ["usuario", "password"]);

    $result = $auth->login($input["usuario"], $input["password"]);
    if (!$result["success"]) {
        api_error($result["message"] ?? "Credenciales inválidas", 401);
    }

    // Compatibilidad con frontend actual: token/usuario/nombre/rol también en raíz.
    api_json([
        "success" => true,
        "error" => false,
        "message" => "Login exitoso",
        "token" => $result["token"] ?? null,
        "usuario" => $result["usuario"] ?? null,
        "nombre" => $result["nombre"] ?? null,
        "rol" => $result["rol"] ?? null,
        "data" => [
            "token" => $result["token"] ?? null,
            "usuario" => $result["usuario"] ?? null,
            "nombre" => $result["nombre"] ?? null,
            "rol" => $result["rol"] ?? null
        ]
    ], 200);
});
