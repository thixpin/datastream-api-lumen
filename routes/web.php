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

// Driver routes that require for orderTracking app
$router->group(['prefix' => 'drivers'], function () use ($router) {
    $router->get('/', 'DriverController@streamIndex');
    $router->get('/{id}', 'DriverController@streamOne');
});

// Order routes that require for orderTracking app
$router->group(['prefix' => 'orders'], function () use ($router) {
    $router->get('/', 'OrderController@streamIndex');
    $router->get('/{id}', 'OrderController@streamOne');
    $router->post('/', 'OrderController@store');
    $router->patch('/{id}/cancel', 'OrderController@cancel');
});

// Seed data route
$router->post('/seed', 'SeedController@seed');
$router->delete('/seed', 'SeedController@clear');

// NoStream routes for testing
$router->group(['prefix' => 'nostream'], function () use ($router) {

    $router->group(['prefix' => 'drivers'], function () use ($router) {
        $router->get('/', 'DriverController@index');
        $router->get('/{id}', 'DriverController@show');
    });

    $router->group(['prefix' => 'customers'], function () use ($router) {
        $router->get('/', 'CustomerController@index');
        $router->get('/{id}', 'CustomerController@show');
    });

    $router->group(['prefix' => 'orders'], function () use ($router) {
        $router->get('/', 'OrderController@index');
        $router->get('/{id}', 'OrderController@show');
    });

});