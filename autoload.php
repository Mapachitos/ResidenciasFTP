<?php

spl_autoload_register(function($class){
    // Convertir las barras diagonales inversas a barras diagonales normales
    $rutaCompleta = str_replace("\\", "/", $class) . ".php";
    // Obtener la posición de la última aparición de la barra diagonal en la ruta
    $posicionBarra = strrpos($rutaCompleta, "/");
    
    // Obtener el nombre del archivo
    $archivo = substr($rutaCompleta, $posicionBarra + 1);

    // Obtener el directorio de la ruta
    $directorio = substr($rutaCompleta, 0, $posicionBarra);

    // Convertir el directorio a minúsculas
    $directorioMinusculas = strtolower($directorio);

    // Ruta completa original
    $rutaOriginal = "../" . $directorio . "/" . $archivo;

    // Ruta completa en minúsculas
    $rutaMinusculas = "../" . $directorioMinusculas . "/" . $archivo;

    // Verificar si el archivo existe en su forma original o en minúsculas
    if(file_exists($rutaOriginal)){
        require_once $rutaOriginal;
    }
    elseif(file_exists($rutaMinusculas)){
        require_once $rutaMinusculas;
    }
    else{
        die("El archivo $class no existe en $rutaOriginal ni en $rutaMinusculas");
    }
});
