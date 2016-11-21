<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Route::get('/datosPais', function(){
//   $data = file_get_contents("dataset/datos.geojson");
//   return response()->json($data);
// });
Route::get('/{lon?},{lat?}',  ['as' => 'Inicio', 'uses' => 'MapController@showMap']);
