<?php

use App\Http\Controllers\Admin\EmpresaAtividadeController;
use App\Http\Controllers\Admin\EmpresaCnaeController;
use App\Http\Controllers\Admin\EmpresasController;
use App\Http\Controllers\Admin\FaturaController;
use App\Http\Controllers\Admin\LicencaController;
use App\Http\Controllers\Admin\NotaController;
use App\Http\Controllers\Admin\ProtocoloController;
use App\Http\Controllers\Admin\UsuariosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth:sanctum', 'verified', 'controle.licenca']], function () {
    Route::group(['prefix' => 'a', 'middleware'=>'controle.acesso', 'as'=>'admin.'], function() {
        Route::get('/', function () {
            return redirect()->route('admin.faturas.index');
        });
        //Route::get('autenticar/usuario/{id}', [UsuariosController::class, 'autenticar'])->name('usuarios.autenticar');
        //Route::resource('photos', PhotoController::class);
        Route::resource('empresas', EmpresasController::class);
        Route::get('/remover-dados/empresa/{id}', [EmpresasController::class, 'removerDados'])->name('empresas.remover-dados');
        Route::get('/api-externa/empresas', [EmpresasController::class, 'empresasApiTecnospeed'])->name('empresas.tecnospeed');
        Route::get('/empresas-por-estado', [EmpresasController::class, 'empresasPorEstado'])->name('empresas.por-estado');

        Route::resource('usuarios', UsuariosController::class);
        Route::resource('empresa-cnaes', EmpresaCnaeController::class);
        Route::resource('empresa-atividades', EmpresaAtividadeController::class);
        Route::resource('licencas', LicencaController::class);
        Route::resource('faturas', FaturaController::class);
        Route::group(['prefix' => 'licencas', 'as'=>'licencas.'], function () {
            Route::get('/licenca/{id}', [LicencaController::class, 'licenca'])->name('licenca');
            Route::get('renovar/{id}', [LicencaController::class, 'renovar'])->name('renovar');
            Route::get('vencimentos', [LicencaController::class, 'vencimentosLicencas'])->name('vencimentos');
        });     
        Route::resource('notas', NotaController::class);     
        Route::resource('protocolos', ProtocoloController::class);    

        Route::get('servico/gerar/{fatura}', [FaturaController::class, 'gerarServico'])->name('faturas.gerar-servico');

        //atualizarRegimeEspecial
        Route::get('atualiza/dados/empresas/{id}', [EmpresasController::class, 'atualizarRegimeEspecial'])->name('empresas.dados-receita');

        Route::get("erros", [NotaController::class, 'errosUsuarios'])->name('erros-sistema');
    });
});
