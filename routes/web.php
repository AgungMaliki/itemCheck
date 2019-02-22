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

//auth
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');


$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'checklists'], function () use ($router) {
        $router->post('/complete', 'ItemController@GetById');
        $router->post('/incomplete', 'ItemController@GetById');
        $router->post('/{checkId}/items', 'itemController@create_checklist');
        $router->get('/{checkId}/items/{itemId}', 'ItemController@getChecklistByItemId');
        $router->get('/{checkId}/items/', 'ItemController@getChecklist');
        $router->patch('/{checkId}/items/{itemId}', 'itemController@update_checklist');
        $router->delete('/{checkId}/items/{itemId}', 'itemController@update_checklist');
});

$router->get('/items/{id}', 'ItemController@GetById');
