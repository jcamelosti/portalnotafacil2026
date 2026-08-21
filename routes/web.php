<?php

use App\Http\Controllers\Emissor\CertificadoController;
use App\Http\Controllers\Emissor\DadosFaturamentoController;
use App\Http\Controllers\Emissor\DashboardController;
use App\Http\Controllers\Emissor\NfseNacional;
use App\Http\Controllers\Emissor\NotaController;
use App\Http\Controllers\Emissor\ProtocoloController;
use App\Http\Controllers\Empresas\CodTribMunCodTribNacController;
use App\Http\Controllers\Empresas\EmpresasController;
use App\Http\Controllers\Empresas\LicencasController;
use App\Http\Controllers\Empresas\NbsController;
use App\Http\Controllers\Empresas\SolicitarCreditoController;
use App\Http\Controllers\InfinitePayWebHookController;
use App\Http\Controllers\Irpj\RelatorioController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Tomadores\TomadoresController;
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

Route::get('/', function () {
    return redirect('login');
});

//Route::get('/', [SiteController::class, 'index'])->name('site.home');

//Impressão de Nota
Route::get('visualizar/nota/{cnpjTomador}/{idNota}', [SiteController::class, 'visualizarNota'])->name('notas.visualizacao-publica');

/*
    Rotas Abertas
*/

//Route::post('/retorno/safe2pay', [\App\Http\Controllers\Safe2PayController::class, 'hook'])->name('safe2pay.webhook');
Route::get('/consultar/cidades/{uf_id}', [SiteController::class, 'consultarCidades'])->name('site.consulta_cidades');
Route::get('/consultar/item-lc/{cnae_id}', [SiteController::class, 'consultarItemLc'])->name('site.consulta_item_lc');
Route::get('/consultar/nbs/{item_lc_id}', [SiteController::class, 'consultarNbs'])->name('site.consulta_nbs');
Route::get('/listar/cidades', [SiteController::class, 'buscarCidades'])->name('site.consulta_cidades_geral');
Route::get('/consultar/cidades/{uf_id}/json', [SiteController::class, 'buscarCidadesJson'])->name('site.consulta_cidades-json');
Route::get('/consultar/estados/json', [SiteController::class, 'buscarEstadosJson'])->name('site.consulta_estados-json');

//MERCADO PAGO - /retorno/mercadopago
//essa rota é sobre aguardar o pagamento do pix e redirecionar o usuário.
Route::get('/checar/retorno/mercadopago/{id}', [MercadoPagoController::class, 'checarPagamento'])->name('mercadopago.retorno');

Route::post('/retorno/mercadopago', [MercadoPagoController::class, 'index'])->name('mercadopago.webhook');//mercadopago - notification_url
Route::post('/webhook/mercadopago/capture', [MercadoPagoController::class, 'webHookResponse'])->name('mercadopago.webhook-response');//mercado pago webhook
Route::post('/webhook/infinitepay/capture', [InfinitePayWebHookController::class, 'webHookResponse'])->name('infinitypay.webhook-response');//infinitypay webhook

Route::get('/foto/{hash}', function ($hash) {
    abort_unless(auth()->check(), 403);

    $path = storage_path("app/public/profile-photos/{$hash}");
    
    abort_unless(file_exists($path), 404);
    
    return response()->file($path);
})->name('foto');

Route::group(['middleware' => ['auth:sanctum', 'verified', 'controle.licenca', 'dados.faturamento']], function () {
    Route::group(['prefix' => 'c'], function() {
        /*Route::get('/pagamento-realizado', [DashboardController::class, 'pagamentoRealizado'])->name('pagamentoRealizado');
        Route::get('/trazernotas/{empresaId}', [\App\Http\Controllers\Emissor\NotaController::class, 'getNotasEmpresa'])->name('sincnotasempresa');
        Route::get('/sinc/notas/{empresaId}', [\App\Http\Controllers\Emissor\NotaController::class, 'sincronizarNotasRecentes'])->name('sincronizarNotasRecentes');*/
    
        Route::get('/', [\App\Http\Controllers\Emissor\DashboardController::class, 'dashboard'])->name('dashboard');
        
        Route::group(['prefix' => 'emissor'], function () {
            Route::get('nota/listagem', [\App\Http\Controllers\Emissor\NotaController::class, 'index'])->name('nota.index');
            Route::get('nota/criar', [\App\Http\Controllers\Emissor\NotaController::class, 'create'])->name('nota.emitir');
            Route::post('nota/salvar', [\App\Http\Controllers\Emissor\NotaController::class, 'store'])->name('notas.store');
            
            Route::get('nota/imprimir/pdf/{prestador}/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'imprimirNota'])->name('notas.pdf');
            Route::get('nota/imprimir/xml/{prestador}/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'baixarXml'])->name('notas.xml');
            Route::get('nota/cancelar/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'cancelarNota'])->name('notas.cancelar');
            Route::get('notas/emitidas',  [\App\Http\Controllers\Emissor\NotaController::class, 'listarNotaEmitidas'])->name('notas.emitidas-listagem');
            Route::get('nota/print/xml/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'visualizarXmlNota'])->name('notas.visualizar-xml');
            Route::any('nota/cancelar/{id}/issnet', [\App\Http\Controllers\Emissor\NotaController::class, 'cancelarNotaIssNet'])->name('notas.cancelar-issnet');
            Route::get('nota/duplicar/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'duplicar'])->name('notas.duplicar');

            //confirmação transmissao notas
            Route::get('nota/transmissao/ok', [\App\Http\Controllers\Emissor\NotaController::class, 'confirmacaoTransmissao'])->name('notas.confirmacao-transmissao');

            Route::get('nota/substuicao/{id}', [\App\Http\Controllers\Emissor\NotaController::class, 'substituirNota'])->name('notas.substitucao');
            Route::post('nota/substituicao/salvar', [\App\Http\Controllers\Emissor\NotaController::class, 'store_substituicao'])->name('notas.store-substituicao');

            Route::get('teste/nfse-nacional', [\App\Http\Controllers\Emissor\NfseNacional::class, 'teste']);

            //novas rotas - 17/08/2026
            Route::get('nfse-nacional/testes', [\App\Http\Controllers\Emissor\TesteNfeNacionalController::class, 'teste']);

            //pesquisar
            Route::get('/obter/tributacao-nacional/por-tributacao-mun', [NotaController::class, 'obterTributacaoNacionalPorAtividadeMun']);
            Route::get('/obter/nbs/por-empresa', [NotaController::class, 'obterNbs']);
        });

        Route::group(['prefix' => 'emissor-nacional-mei'], function () {
            Route::group(['prefix' => 'servicos-prestados'], function () {
                Route::get('servicos/index/', [NfseNacional::class, 'index'])->name('servicos-mei.index');
                Route::get('servicos/novo/', [NfseNacional::class, 'novo'])->name('servicos-mei.novo');
                Route::post('servicos/salvar/', [NfseNacional::class, 'salvar'])->name('servicos-mei.salvar');

                Route::get('servicos/editar/{servico}', [NfseNacional::class, 'editar'])->name('servicos-mei.editar');
                Route::put('servicos/atualizar/{servico}', [NfseNacional::class, 'atualizar'])->name('servicos-mei.atualizar');
                Route::get('servicos/show/{servico}', [NfseNacional::class, 'exibir'])->name('servicos-mei.show');
                Route::delete('servicos/destroy/{id}', [NfseNacional::class, 'destroy'])->name('servicos-mei.destroy');

                Route::post('servicos/transmitir/{servico}', [NfseNacional::class, 'transmitir'])->name('servicos-mei.transmitir');
                Route::get('servicos/danfe/{servico}', [NfseNacional::class, 'danfe'])->name('servicos-mei.danfe');
            });        
        });

        //Dados Para Faturamento
        Route::get('cliente/dados-faturamento/adicionar', [DadosFaturamentoController::class, 'create'])->name('dados-faturamento.create');
        Route::get('cliente/dados-faturamento/editar', [DadosFaturamentoController::class, 'edit'])->name('dados-faturamento.edit');
        Route::post('cliente/dados-faturamento/store', [DadosFaturamentoController::class, 'store'])->name('dados-faturamento.store');
        Route::put('cliente/dados-faturamento/update', [DadosFaturamentoController::class, 'update'])->name('dados-faturamento.update');

        Route::any('empresas/selecionar/empresa', [EmpresasController::class, 'selecionarEmpresa'])->name('empresas.selecionar-empresa');
        Route::any('empresas/sincronizar/{id}', [EmpresasController::class, 'sincronizarDadosIss'])->name('empresas.sincDataIssNet');
        Route::any('tomadores/selecionar/tomador', [TomadoresController::class, 'selecionarTomador'])->name('tomadores.selecionar-tomador');
        Route::get('seq/{id}/{numNota}', [EmpresasController::class, 'ajuste'])
            ->name('empresas.ajuste');

        Route::any('/empresas/adicionar/por-cnpj', [EmpresasController::class, 'buscarDadosEmpresa'])->name('empresas.adicionar');
        
        Route::get('listar/solicitacoes/acesso/empresa', [EmpresasController::class, 'listagemSolicitacaoCompartilhamentoEmpresa'])->name('empresas.listar-solicitacao-acesso-empresa');
        Route::get('solicitar/acesso/empresa', [EmpresasController::class, 'novaSolicitacaoCompartilhamentoEmpresa'])->name('empresas.nova-solicitar-acesso-empresa');
        Route::post('efetivar/solicitacao/acesso/empresa', [EmpresasController::class, 'gravarSolicitacaoCompartilhamentoEmpresa'])->name('empresas.gravar-solicitar-acesso-empresa');
        
        Route::get('solicitacoes/acesso/empresa', [EmpresasController::class, 'listagemCompartilhamentoEmpresa'])->name('empresas.list-solicitar-acesso-empresa');
        Route::any('{id}/permissao/solicitacao/acesso/empresa', [EmpresasController::class, 'alteraSolicitacaoCompartilhamentoEmpresa'])->name('empresas.alterar-solicitar-acesso-empresa');
    
        Route::group(['prefix' => 'area-cliente'], function () {
            Route::get('/', [\App\Http\Controllers\Emissor\DashboardController::class, 'index'])->name('area-cliente');

            Route::resource('empresas', EmpresasController::class);
            Route::prefix('correlacao/codtribmun-codtribnac/{empresa}')->group(function () {
                Route::get(
                    'listagem',
                    [CodTribMunCodTribNacController::class,'index']
                )->name('codtrimun-codtribnac.index');
                
                Route::get(
                    'criar',
                    [CodTribMunCodTribNacController::class,'create']
                )->name('codtrimun-codtribnac.create');
                
                Route::post(
                    'store',
                    [CodTribMunCodTribNacController::class,'store']
                )->name('codtrimun-codtribnac.store');

                Route::get(
                    '{correlacaoTribMunTribNac}/editar',
                    [CodTribMunCodTribNacController::class,'edit']
                )->name('codtrimun-codtribnac.edit');

                Route::put(
                    '{correlacaoTribMunTribNac}/atualizar',
                    [CodTribMunCodTribNacController::class,'update']
                )->name('codtrimun-codtribnac.update');
            });

            Route::prefix('empresa/nbs/{empresa}')->group(function () {
                Route::get(
                    'listagem',
                    [NbsController::class,'index']
                )->name('empresa-nbs.index');
                
                Route::get(
                    'criar',
                    [NbsController::class,'create']
                )->name('empresa-nbs.create');
                
                Route::post(
                    'store',
                    [NbsController::class,'store']
                )->name('empresa-nbs.store');

                Route::get(
                    '{empresaNbs}/editar',
                    [NbsController::class,'edit']
                )->name('empresa-nbs.edit');

                Route::put(
                    '{empresaNbs}/atualizar',
                    [NbsController::class,'update']
                )->name('empresa-nbs.update');
            });

            Route::get('/empresas/list/json', [EmpresasController::class, 'json'])->name('empresas.json');
            Route::resource('tomadores', TomadoresController::class);
            // Rota adicional para outro método de update
            Route::put('/tomadores/{id}/exterior', [TomadoresController::class, 'update_ext'])
                ->name('tomadores.update_ext');
            
            Route::get('/tomadores/create/exterior', [TomadoresController::class, 'create_ext'])
                ->name('tomadores.create_ext');

            Route::post('/tomadores/store_ext', [TomadoresController::class, 'store_ext'])
                ->name('tomadores.store_ext');

                
            Route::get('consultar/tomadores/buscar', [TomadoresController::class,'buscar'])
                ->name('tomadores.buscar');
            
            Route::any('/cliente/adicionar', [TomadoresController::class, 'buscarDadosEmpresa'])->name('tomadores.adicionar');

            Route::group(['prefix' => 'licenciamento', 'as'=>'licenciamento.'], function () {
                Route::get('/', [LicencasController::class, 'index'])->name('licenciamento.index');
            });

            Route::group(['prefix' => 'empresas-certificados', 'as'=>'empresas-certificados.'], function () {
                Route::any('/', [CertificadoController::class, 'index'])->name('index');
                Route::any('/criar', [CertificadoController::class, 'create'])->name('create');
                Route::any('/gravar', [CertificadoController::class, 'store'])->name('store');
                Route::any('/editar/{id}', [CertificadoController::class, 'edit'])->name('edit');
                Route::any('/atualizar/{id}', [CertificadoController::class, 'update'])->name('update');
                Route::any('/remover/{id}', [CertificadoController::class, 'destroy'])->name('destroy');
            });
        });

        Route::group(['prefix' => 'licenca', 'as'=>'licenca.'], function () {
            Route::get('empresa/{id}/renovacao', [LicencasController::class, 'renovacao'])->name('renovacao');
            Route::get('empresas/renovacao/ordem-pagamento/{plano}', [LicencasController::class, 'gerarOrdemPagamento'])->name('gerar-fatura');
            Route::get('gerar-chave-pix/{faturaId}', [LicencasController::class, 'gerarPix'])->name('gerar-pix');
            //Route::get('gerar-chave-pix/{fatura_id}', ['as' => 'gerar-pix', 'uses' => 'LicenciamentoController@gerarPix']);
            //Route::get('gerar-boleto/{fatura_id}', ['as' => 'gerar-boleto', 'uses' => 'LicenciamentoController@boleto']);
            //Route::any('cartao-credito/{fatura_id}', ['as' => 'gerar-cartaocredito', 'uses' => 'LicenciamentoController@cartaoCredito']);
            //Route::any('cartao-debito/{fatura_id}', ['as' => 'gerar-cartaodebito', 'uses' => 'LicenciamentoController@cartaoDebito']);
        });

        //Faturas dos Clientes
        Route::get('faturas', [\App\Http\Controllers\Empresas\FaturaController::class, 'index'])->name('faturas.index');
        Route::get('faturas/{id}', [\App\Http\Controllers\Empresas\FaturaController::class, 'show'])->name('faturas.show');

        //CREDITOS
        Route::group(['prefix' => 'creditos', 'as'=>'creditos.'], function () {
            Route::get('solicitar', [SolicitarCreditoController::class, 'solicitarCredito'])->name('emitir');
            Route::post('confirmar', [SolicitarCreditoController::class, 'store'])->name('confirmar');
        });

        //RELATÓRIO IMPOSTO DE RENDA
        Route::group(['prefix' => 'imposto-de-renda', 'as'=>'irpj.'], function () {
            Route::get('/', [RelatorioController::class, 'index'])->name('index');
            Route::post('/upload', [RelatorioController::class, 'upload'])->name('upload');
        });

        Route::get('servicos/tomador', [\App\Http\Controllers\Emissor\NotaController::class, 'servicosTomados'])->name('servicos.tomados');
    });
});