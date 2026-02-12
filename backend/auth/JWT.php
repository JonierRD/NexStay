<?php

class JWT {
    private $secret;
    private $exp;

    public function __construct($secret, $exp) {
        $this->secret = $secret;
        $this->exp = $exp;
    }

    // ==========================
    // Crear token
    // ==========================
    public function encode($payload) {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

        // añadir fechas
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->exp;

        $payload = json_encode($payload);

        // convertir a base64 seguro
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        // firmar token
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    // ==========================
    // Verificar token
    // ==========================
    public function decode($token) {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception("Token inválido.");
        }

        list($header, $payload, $signature) = $parts;

        // verificar firma
        $check = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
        $check = $this->base64UrlEncode($check);

        if ($signature !== $check) {
            throw new Exception("Firma inválida.");
        }

        // convertir payload a array
        $data = json_decode($this->base64UrlDecode($payload), true);

        // verificar expiración
        if (isset($data['exp']) && time() > $data['exp']) {
            throw new Exception("Token expirado.");
        }

        return $data;
    }

    // ==========================
    // Helpers
    // ==========================
    private function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64UrlDecode($text) {
        $remainder = strlen($text) % 4;
        if ($remainder !== 0) {
            $padlen = 4 - $remainder;
            $text .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $text));
    }
}
