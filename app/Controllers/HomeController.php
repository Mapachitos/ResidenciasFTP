<?php

namespace App\Controllers;

use App\Models\Home;

class HomeController
{
    public function index()
    {
        $home = new Home();

        //Usa find(), query() y first()
        //return $home->find(1);
        //Usa all(), query() y get()
        //return $home->all();
        //Usa where(), query() y get()
        //return $home->where('_id','>', '1')->where('method', 'POST')->get();
        //create(), query() y find()
        /* return $home->create([
            'route' => 'Prueba2',
            'method' => 'Prueba2',
            'params' => json_encode(['Prueba2'])
        ]); */
        //update(), query() y find()
        /* return $home->update(10, [
            'route' => 'Prueba10',
            'method' => 'Prueba10',
            'params' => json_encode(['Prueba10'])
        ]); */
        //delete(), query()
        //return $home->delete(10);
    }
}
