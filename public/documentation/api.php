<?php
require("../../vendor/autoload.php");

$openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'] . '/ResidenciasFTP/app/Controllers']);

header('Content-Type: application/x-json');
echo $openapi->toJSON();