<?php

namespace App\Controllers;

use App\Models\Ftp\ModelFtp;

class FtpController
{
    /**
     * Obtiene un archivo específico del servidor FTP.
     *
     * Esta función instancia la clase ModelFtp para interactuar con el servidor FTP
     * y devuelve el archivo 'Prueba.docx'.
     *
     * @return array La respuesta del servidor, incluyendo el contenido del archivo.
     */
    public function index()
    {
        $ftpServer = new ModelFtp();
        return $ftpServer->getFile('Prueba.docx');
    }

}
