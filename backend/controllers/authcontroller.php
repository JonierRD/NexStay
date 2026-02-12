<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/../auth/JWT.php';
require_once __DIR__ . '/../models/usuarios.php';
require_once __DIR__ . '/../api/response.php';

api_guard(function () {
    $config = include __DIR__ . '/../auth/config.php';
    $jwt = new JWT($config['secret'], $config['token_exp']);
    $usuariosModel = new Usuarios();
    $op = $_GET['op'] ?? '';

    switch($op) {
        case 'login':
            $data = api_input_json();
            api_require_fields($data, ['usuario', 'password']);

            $user = $usuariosModel->obtenerUsuarioPorUsuario($data['usuario']);
            if (!$user) {
                api_error('Usuario no encontrado', 404);
            }

            if (!password_verify($data['password'], $user['password'])) {
                api_error('Credenciales inválidas', 401);
            }

            $payload = [
                'sub' => $user['id'],
                'usuario' => $user['usuario'],
                'rol' => $user['rol']
            ];

            $token = $jwt->encode($payload);
            unset($user['password']);

            api_success('Login exitoso', [
                'token' => $token,
                'user' => $user
            ]);

        default:
            api_error('Operación no válida', 400);
    }
});

