<?php

namespace Lib;
use Lib\Responses;

class Route
{
    
    private static $routes = [];

    public static function get($uri, $callback, $body = false)
    {
        $uri = trim($uri, "/");
        self::$routes['GET'][$uri] = [$callback, $body];
    }
    public static function post($uri, $callback, $body = false)
    {
        $uri = trim($uri, "/");
        self::$routes['POST'][$uri] = [$callback, $body];
    }
    public static function put($uri, $callback, $body = false)
    {
        $uri = trim($uri, "/");
        self::$routes['PUT'][$uri] = [$callback, $body];
    }
    public static function delete($uri, $callback, $body = false)
    {
        $uri = trim($uri, "/");
        self::$routes['DELETE'][$uri] = [$callback, $body];
    }

    private static function getUri()
    {
        //Eliminar ResidenciasFTP/public/
        $uriSave = $_SERVER['REQUEST_URI'];
        $parts = explode("/public/", $uriSave);
        if (count($parts) > 1) {
            $uri = $parts[1];
            $uri = trim($uri, "/");
        } else {
            $uri = "";
        }
        return $uri;
    }

    public static function dispatch()
    {
        /* echo json_encode($_SERVER['REQUEST_METHOD'] . ' ' . self::getUri() . "\n");
        return; */
        $responses = new Responses('Route-dispatch');
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        try {
            $uri = self::getUri();
            $method = $_SERVER['REQUEST_METHOD'];

            foreach (self::$routes[$method] as $route => $arrFuncBody) {

                //#A01
                if (strpos($route, ":") !== false) {
                    //Reemplazar :slug por [a-zA-Z]+ (solo para compilacion de rutas)
                    $route = preg_replace("#:[a-zA-Z0-9]+#", "([a-zA-Z0-9]+)", $route);
                }

                //Compara si si la ruta es igual a la uri actual
                //#A02
                if (preg_match("#^$route$#", $uri, $matches)) {
                    $callback = $arrFuncBody[0];
                    $bodyFlag = $arrFuncBody[1];
                    //Por si se requiere el body y no se manda
                    if ($bodyFlag) {
                        //Si el content-type es application/json
                        if (isset($_SERVER['CONTENT_TYPE'])) {
                            //funciona
                            if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                                header('Content-Type: application/json');
                                $request = file_get_contents('php://input');
                            }
                            //Si el content-type es multipart/form-data
                            else if (strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false){
                                //funciona -----
                                header('Content-Type: application/json');
                                if(!isset($_FILES['file'])){
                                    echo json_encode($responses->error_400("File not found"));
                                    return;
                                }
                                $file = $_FILES['file'];
                                if ($file['error'] !== UPLOAD_ERR_OK) {
                                    //nofunciona ----
                                    echo json_encode($responses->error_400("File upload error: " . $file['error']));
                                }
                                $bodyFlag = false;// no es
                            } else {
                                echo json_encode($responses->error_400('Content-Type no soportado:'.$_SERVER['CONTENT_TYPE']));
                                return;
                            }
                        } else {
                            echo json_encode($responses->error_400('Content-Type no proporcionado'));
                            return;
                        }
                    }
                    //matches son coincidencias que obtenemos de la ruta
                    //De acuerdo a la expresion regular "([a-zA-Z0-9]+)" que se definio en #A01
                    $params = array_slice($matches, 1);

                    
                    // Si es una función anónima    
                    if (is_callable($callback)) {
                        if ($bodyFlag) {
                            $response = $callback($request, ...$params);
                        } else {
                            $response = $callback(...$params);
                        }
                    }
                    // Si es una función desde un controlador
                    else if (is_array($callback)) {
                        $controller = new $callback[0]();
                        if ($bodyFlag) {
                            $response = $controller->{$callback[1]}($request, ...$params);
                        } else {
                            $response = $controller->{$callback[1]}(...$params);
                        }
                    }
                    //Si es un array o un objeto lo convierte a JSON
                    if (is_array($response) || is_object($response)) {
                        
                        echo json_encode($response);
                        return;
                    }
                    echo $response;
                    return;
                }
            }
            http_response_code(404);
            echo json_encode($responses->error_404());
        } catch (\Throwable $th) {
            echo json_encode($responses->error_500());
        }
    }
}
