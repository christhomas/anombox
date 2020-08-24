<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => 'cors'], function () use ($router) {
    $router->options('{path:.*}', 'Controller@cors');

    $router->group(['prefix' => '/anombox/v1'], function() use ($router){
        $router->post('/upload', 'Controller@upload');

        $router->group(['middleware' => 'auth'], function () use ($router){
            $router->get('/list', 'Controller@list');
        });

        $router->post('/login', 'Controller@login');
    });
});
