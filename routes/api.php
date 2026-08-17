<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\NotaController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TomadorController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(RegisterController::class)->group(function(){
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'login');
    });    
});

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('emissor', NotaController::class);
    Route::post('emissor/cancelar', [NotaController::class, 'cancelarNota']);

    Route::prefix('usuarios')
        ->controller(UsuarioController::class)
        ->group(function () {
            Route::get('ultimos', 'ultimos');
            Route::get('{id}', 'show');
        });

    //Route::apiResource('empresas', EmpresaController::class);
});