<?php

namespace App\Services;
use Firebase\JWT\JWT;

/**
 * Servicio para la generación de tokens JWT.
 */
class JwtService
{
    /**
     * Clave secreta utilizada para la codificación y decodificación de los tokens JWT.
     *
     * @var string
     */
    private $key = JWT_SECRET_KEY;

    /**
     * Genera un token JWT utilizando los datos del usuario.
     *
     * @param array $dataUser Los datos del usuario para incluir en el token.
     * @return string El token JWT generado.
     */
    public function getToken($dataUser)
    {
        $time = time();
        $expirationTime = $time + (365 * 24 * 60 * 60); // 1 año de duración por defecto
        //$expirationTime = $time + 500; // Tiempo de expiración más corto para pruebas
        $payload = [
            'iat' => $time,
            'exp' => $expirationTime,
            'username' => $dataUser['username'],
            'password' => md5($dataUser['password']),
            'typeuser' => $dataUser['typeuser'],
            'status' => $dataUser['status']
        ];
        return JWT::encode($payload, $this->key, 'HS256');
    }
}