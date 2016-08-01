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

$app->get('/', function () use ($app) {
    return view('index');
});


/**
* Competition
*/
$app->group(['prefix' => 'api/competition', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('get/all', 'CompetitionController@index');
    $app->get('get/{id}', 'CompetitionController@get');
    $app->post('create', 'CompetitionController@create');
    $app->put('update', 'CompetitionController@update');
    $app->delete('delete', 'CompetitionController@delete');
});

/**
* Category
*/
$app->group(['prefix' => 'api/category', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('get/all', 'CategoryController@index');
    $app->get('get/{id}', 'CategoryController@get');
    $app->post('create', 'CategoryController@create');
    $app->put('update', 'CategoryController@update');
    $app->delete('delete', 'CategoryController@delete');
});

/**
* Route
*/
$app->group(['prefix' => 'api/route', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('get/all/{id}', 'RouteController@index');
    $app->get('get/{id}', 'RouteController@get');
    $app->post('create', 'RouteController@create');
    $app->put('update', 'RouteController@update');
    $app->delete('delete', 'RouteController@delete');
});

/**
* Competitor
*/
$app->group(['prefix' => 'api/competitor', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('get/all/{id}', 'CompetitorController@index');
    $app->get('get/{id}', 'CompetitorController@get');
    $app->post('create', 'CompetitorController@create');
    $app->put('update', 'CompetitorController@update');
    $app->delete('delete', 'CompetitorController@delete');
});

/**
* Results
*/
$app->group(['prefix' => 'api/result', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('get/all/{id}', 'ResultController@index');
    $app->get('get/{id}', 'ResultController@get');
    $app->post('create', 'ResultController@create');
    $app->put('update', 'ResultController@update');
    $app->delete('delete', 'ResultController@delete');
});
