<?php

use Lib\Route;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\FtpController;
use App\Controllers\AnteproyectoController;

Route::get('/', [HomeController::class, 'index']);//No sirve


/************************************** UserController **************************************/
//Ruta para obtener todos los usuarios
//asignado el controlador UserController y el metodo index
Route::get('/users', [UserController::class, 'index']);//yaizel
//Ruta login, asignado el controlador UserController y el metodo index
//true, indica que necesita parametros por el body (json)
Route::get('/login', [UserController::class, 'show'], true);//jabs
Route::post('/register', [UserController::class, 'store'], true);// ---- LI ----

/************************************** AnteproyectoController **************************************/
//Ruta para obtener todos los anteproyectos
//asignado el controlador AnteproyectoController y el metodo index
Route::get('/anteproyecto', [AnteproyectoController::class, 'index']);//yaizel

//Ruta para obtener un proyecto del servidor ftp
//id -> nombre del archivo sin extension
//ext -> extension del archivo (pdf, docx, etc)
//example: /anteproyecto/fileprueba/docx
Route::get('/anteproyecto/:id/:ext', [AnteproyectoController::class, 'show']);//jabs

Route::post('/anteproyecto', [AnteproyectoController::class, 'store'], true);// ---- LI ----

//Ruta para eliminar un anteproyecto, elimina por body
Route::delete('/anteproyecto', [AnteproyectoController::class, 'delete'], true);//yibran

//Ruta para eliminar un anteproyecto elimina por url
Route::delete('/anteproyecto/:id/:ext', [AnteproyectoController::class, 'destroy']);// ---- LI ----

Route::dispatch();

