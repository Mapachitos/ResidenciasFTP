<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware
{
    /**
     * Maneja la verificación del token de autenticación.
     *
     * Esta función obtiene el token del encabezado de la solicitud y verifica su validez.
     * Si el token es válido, retorna los datos decodificados del token.
     * Si el token no es válido o no se proporciona, envía una respuesta HTTP 401 y termina la ejecución del script.
     *
     * @return array|null Los datos decodificados del token si es válido, o termina la ejecución del script en caso de error.
     */
    public function handle()
    {
        $token = $this->obtenerTokenDeEncabezado();
        if ($token) {
            $datosToken = $this->verificarToken($token);
            if ($datosToken) {
                return $datosToken;  // El token es válido, retorna los datos decodificados
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Acceso denegado, token inválido"]);
                exit();  // Detiene la ejecución del script
            }
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Acceso denegado, no se proporcionó el token"]);
            exit();  // Detiene la ejecución del script
        }
    }


    /**
     * Verifica la validez de un token de autenticación JWT.
     *
     * Esta función decodifica y verifica la validez de un token JWT utilizando la clave secreta proporcionada.
     * Si el token es válido, devuelve los datos decodificados en forma de array.
     * Si el token es inválido o ha expirado, devuelve null.
     *
     * @param string $token El token de autenticación JWT a verificar.
     * @return array|null Los datos decodificados del token si es válido, o null si el token es inválido o ha expirado.
     */
    private function verificarToken($token) {
        $key = JWT_SECRET_KEY;
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;  // Token inválido o expirado
        }
    }

    /**
     * Obtiene el token de autenticación del encabezado de la solicitud.
     *
     * Esta función busca y extrae el token de autenticación del encabezado "Authorization" de la solicitud HTTP.
     * Si no se encuentra el encabezado, o si el formato del encabezado es incorrecto, devuelve null.
     *
     * @return string|null El token de autenticación JWT si se encuentra en el encabezado de la solicitud, o null si no se proporciona el token.
     */
    private function obtenerTokenDeEncabezado() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return null;  // No se encontró el encabezado
        }
        $authHeader = $headers['Authorization'];
        $parts = explode(" ", $authHeader);
        if (count($parts) < 2) {
            return null;  // Formato incorrecto del encabezado
        }
        $scheme = $parts[0];
        $token = $parts[1];
        if (strcasecmp($scheme, "Bearer") != 0) {
            return null;  // No es un token de portador
        }
        return $token;
    }

}

