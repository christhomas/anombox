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

session_start();

$router->group(['middleware' => 'cors'], function () use ($router) {
    $router->options('{path:.*}', 'Controller@cors');

    $router->group(['prefix' => '/anombox/v1'], function() use ($router){
        $router->post('/upload', 'Controller@upload');

        $router->get('admin', function() {
            return view('login');
        });

        $router->get('/view', function(){
            return view('view');
        });

        $router->group(['middleware' => 'auth'], function () use ($router){
            $router->get('/list', 'Controller@list');
            $router->delete('/delete/{filename}', 'Controller@delete');
            $router->get('/download/{filename}', 'Controller@download');
        });

        $router->post('/login', 'Controller@login');
    });
});
