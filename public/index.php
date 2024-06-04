<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Carga configuraciones y archivos específicos del proyecto.
require_once __DIR__ . '/../config/ftp.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt.php';

// Sistema de enrutamiento
require_once __DIR__ . '/../routes/api.php';


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

