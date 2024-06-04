<?php

namespace App\Controllers;

use App\Models\Ftp\Anteproyecto;
use Lib\Responses;
use App\Middlewares\AuthMiddleware;
use Exception;



class AnteproyectoController
{
    /**
     * @OA\Get(
     *     path="/anteproyectos",
     *     summary="Obtiene una lista de archivos",
     *     tags={"Anteproyecto"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de archivos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index()
    {
        //Aplicar Middleware en la ruta, para verificar el token
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        //Instanciar modelo Anteproyecto
        $ftpServer = new Anteproyecto();
        return $ftpServer->listFiles();
    }

    /**
     * @OA\Get(
     *     path="/anteproyectos/{id}.{ext}",
     *     summary="Muestra y descarga un archivo específico",
     *     tags={"Anteproyecto"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="El identificador del archivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="ext",
     *         in="path",
     *         description="La extensión del archivo",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo descargado",
     *         @OA\Schema(type="file")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show($id, $ext)
    {
        //Aplicar Middleware en la ruta, para verificar el token
        //$middleware = new AuthMiddleware();
        //$middleware->handle();

        //Instanciar clase Responses, que contiene respuestas predeterminadas
        $responses = new Responses('AnteproyectoController-index');

        //Instanciar clase Anteproyecto
        //Contiene métodos para interactuar con el servidor FTP ($ftpServer)
        $ftpServer = new Anteproyecto();
        //archivo .pdf
        $result = $ftpServer->getFile($id . '.' . $ext);

        if ($result['status'] == 'error') {
            return $result;
        } else if ($result['status'] == 'success') {
            $local_file = $result['local_file'];
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($local_file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($local_file));
            flush();
            readfile($local_file);
            unlink($local_file);
        } else {
            //Error 500 internal server error
            return $responses->error_500();
        }
    }

    /**
     * @OA\Post(
     *     path="/anteproyectos",
     *     summary="Sube un archivo al servidor FTP",
     *     tags={"Anteproyecto"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     description="Archivo a subir",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo subido correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se proporcionó un archivo"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store()
    {
        try {
            //Aplicar Middleware en la ruta, para verificar el token
            $middleware = new AuthMiddleware();
            $middleware->handle();

            $responses = new Responses('AnteproyectoController-store');
            $ftpServer = new Anteproyecto();
            
            if (!isset($_FILES['file'])) {
                return $responses->error_400('No se proporcionó un archivo');
            }
            $file = $_FILES['file'];
            return $ftpServer->uploadFile($file);
        } catch (Exception $e) {
            return $responses->error_500();
        }
    }

    /**
     * @OA\Delete(
     *     path="/anteproyectos/{id}.{ext}",
     *     summary="Elimina un archivo específico del servidor FTP",
     *     tags={"Anteproyecto"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="El identificador del archivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="ext",
     *         in="path",
     *         description="La extensión del archivo",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function destroy($id, $ext)
    {
        //Aplicar Middleware en la ruta, para verificar el token
        $middleware = new AuthMiddleware();
        $middleware->handle();

        $ftpServer = new Anteproyecto();
        return $ftpServer->deleteFile($id . '.' . $ext);
    }

   /**
     * @OA\Delete(
     *     path="/anteproyectos/delete",
     *     summary="Elimina un archivo especificado del servidor FTP",
     *     tags={"Anteproyecto"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="file",
     *                 description="Nombre del archivo a eliminar",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se proporcionó nombre de archivo"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function delete($file)
    {
        //Aplicar Middleware en la ruta, para verificar el token
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        //Respuestas predeterminadas
        $responses = new Responses('AnteproyectoController-delete');
        
        //Convertir el body a un array
        $file = json_decode($file, true);
        
        if (!isset($file['file'])) {
            return $responses->error_400('No se proporcionó nombre de archivo');
        }
        
        $ftpServer = new Anteproyecto();
        
        //Ejecuta el método deleteFile (envía el nombre del archivo a eliminar)
        return $ftpServer->deleteFile($file['file']);
    }
}
