<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'drivers'], function () use ($router) {
    $router->get('/', 'DriverController@streamIndex');
    $router->get('/{id}', 'DriverController@streamOne');
});

$router->group(['prefix' => 'nostream'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });
    $router->get('/drivers', 'DriverController@index');
    $router->get('/drivers/{id}', 'DriverController@show');
});