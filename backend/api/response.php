<?php

declare(strict_types=1);

if (!function_exists('api_json')) {
    function api_json(array $payload, int $code = 200): void
    {
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=UTF-8");
        }
        http_response_code($code);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('api_success')) {
    function api_success(string $message = 'OK', $data = null, int $code = 200): void
    {
        api_json([
            "success" => true,
            "error" => false,
            "message" => $message,
            "data" => $data
        ], $code);
    }
}

if (!function_exists('api_error')) {
    function api_error(string $message, int $code = 400, $data = null): void
    {
        api_json([
            "success" => false,
            "error" => true,
            "message" => $message,
            "data" => $data
        ], $code);
    }
}

if (!function_exists('api_input_json')) {
    function api_input_json(): array
    {
        $raw = file_get_contents("php://input");
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            api_error("JSON inválido en el cuerpo de la petición", 400);
        }

        return $decoded;
    }
}

if (!function_exists('api_require_fields')) {
    function api_require_fields(array $input, array $required): void
    {
        $missing = [];
        foreach ($required as $field) {
            if (!array_key_exists($field, $input) || $input[$field] === null || $input[$field] === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            api_error("Datos incompletos", 400, ["faltan" => $missing]);
        }
    }
}

if (!function_exists('api_entity_exists')) {
    function api_entity_exists(PDO $conn, string $table, $id, string $idField = 'id'): bool
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $idField)) {
            return false;
        }

        $stmt = $conn->prepare("SELECT 1 FROM {$table} WHERE {$idField} = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }
}

if (!function_exists('api_guard')) {
    function api_guard(callable $callback): void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            api_error("Error interno del servidor", 500, ["detalle" => $e->getMessage()]);
        }
    }
}

