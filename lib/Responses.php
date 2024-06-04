<?php

namespace Lib;

    /**
 * Clase para generar respuestas predefinidas.
 */
class Responses
{
    /**
     * Código de error utilizado para la construcción de respuestas.
     *
     * @var string
     */
    public $response = [
        'status' => "ok",
        "result" => array(),
        "error-code" => ""
    ];

    /**
     * Construye un objeto Responses.
     *
     * @param string $file El código de error a utilizar en la respuesta.
     */
    public function __construct($file)
    {
        $this->response['error-code'] = $file;
    }

    /**
     * Genera una respuesta de error 405 - Método no permitido.
     *
     * @return array La respuesta generada.
     */
    public function error_405(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "405",
            "error_msg" => "Método no permitido"
        );
        http_response_code(405);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 401 - No autorizado.
     *
     * @param string $msg El mensaje de error personalizado.
     * @return array La respuesta generada.
     */
    public function error_401($msg = "No Autorizado"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "401",
            "error_msg" => $msg
        );
        http_response_code(401);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 200 - Datos incorrectos.
     *
     * @param string $msg El mensaje de error personalizado.
     * @return array La respuesta generada.
     */
    public function error_200($msg = "Datos incorrectos"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "200",
            "error_msg" => $msg
        );
        http_response_code(200);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 400 - Datos incorrectos o incompletos.
     *
     * @param string $msg El mensaje de error personalizado.
     * @return array La respuesta generada.
     */
    public function error_400($msg = "Datos enviados incorrectos o incompletos"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "400",
            "error_msg" => $msg
        );
        http_response_code(400);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 404 - No encontrado.
     *
     * @return array La respuesta generada.
     */
    public function error_404(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "404",
            "error_msg" => "Not Found "
        );
        http_response_code(404);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 404 - Documento no encontrado.
     *
     * @return array La respuesta generada.
     */
    public function errorDoc_404(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "404",
            "error_msg" => "Documento no encontrado "
        );
        http_response_code(404);
        return $this->response;
    }

    /**
     * Genera una respuesta de error 500 - Error interno del servidor.
     *
     * @param string $valor El mensaje de error personalizado.
     * @return array La respuesta generada.
     */
    public function error_500($valor = "Error interno del servidor"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "500",
            "error_msg" => $valor
        );
        http_response_code(500);
        return $this->response;
    }
}
