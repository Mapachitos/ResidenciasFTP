<?php

namespace App\Controllers;

use Lib\Responses;
use App\Middlewares\AuthMiddleware;
use App\Models\User;
use App\Services\JwtService;

/**
 * @OA\Info(title="Api Documentos Servidor FTP", version="1.0")
 */
class UserController
{
    /**
     * @OA\Get(
     *     path="/ResidenciasFTP/public/users",
     *     summary="Obtener todos los usuarios",
     *     description="Retorna todos los usuarios en el sistema.",
     *     tags={"Usuario"},
     *     @OA\Response(
     *         response=200,
     *         description="Éxito. Retorna la lista de usuarios."
     *     )
     * )
     */
    public function index()
    {
        // Importar Middleware
        $middleware = new AuthMiddleware();
        $middleware->handle();
        $user = new User();
        return $user->all();
    }

    /**
     * @OA\Get(
     *     path="/ResidenciasFTP/public/login",
     *     summary="Obtener un usuario por ID",
     *     description="Retorna un usuario por su ID.",
     *     tags={"Usuario"},
     * 
     * @OA\RequestBody(
     *     required=true,
     *     description="Cuerpo de la solicitud para asociar un alumno a un proyecto",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"username", "password"},
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="ID del alumno",
     *                 example="user123"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="ID del proyecto",
     *                 example="12345"
     *             )
     *         )
     *     )
     * ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Éxito. Retorna los detalles del usuario."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró el usuario."
     *     )
     * )
     */
    public function show($request)
    {
        $response = new Responses('UserController-index');
        $user = new User();
        if (isset($request)) {
            $request = json_decode($request, true);
            if (!isset($request['username']) || !isset($request['password'])) {
                return $response->error_400();
            } else {
                $dataUser = $user->where('username', $request['username'])->where('password', md5($request['password']))->getFirst();
                if (!empty($dataUser)) {
                    return json_encode([
                        'status' => 'success',
                        'data' => $dataUser
                    ]);
                } else {
                    return $response->error_200();
                }
            }
        } else {
            return $response->error_400();
        }
    }
    /**
     * @OA\Post(
     *     path="/ResidenciasFTP/public/users",
     *     summary="Crear un nuevo usuario",
     *     description="Crea un nuevo usuario en el sistema.",
     *     tags={"Usuario"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     description="Nombre de usuario",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Contraseña del usuario",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="typeuser",
     *                     description="Tipo de usuario",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Éxito. Retorna los detalles del nuevo usuario creado."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros inválidos."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor."
     *     )
     * )
     */
    public function store($request)
    {
        $response = new Responses('UserController-store');
        $user = new User();
        if (isset($request)) {
            $request = json_decode($request, true);
            if (!isset($request['username']) || !isset($request['password']) || !isset($request['typeuser'])) {
                return $response->error_400();
            } else {
                $request['status'] = 1;
                $jwt = new JwtService();
                $token = $jwt->getToken($request);
                $userCreate = $user->create([
                    'username' => $request['username'],
                    'password' => md5($request['password']),
                    'status' => $request['status'],
                    'typeuser' => $request['typeuser'],
                    'token' => $token
                ]);

                if ($userCreate) {
                    return json_encode([
                        'status' => 'success',
                        'data' => $userCreate,
                    ]);
                } else {
                    return $response->error_500();
                }
            }
        } else {
            return $response->error_400();
        }
    }
}
