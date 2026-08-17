<?php

namespace App\Http\Controllers;

use App\Models\CreditoUser;
use App\Models\Fatura;
use App\Models\HistoricoCredito;
use App\Models\License;
use App\Models\NotificacaoGatewayPagamento;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InfinitePayWebHookController extends Controller
{
    private $faturaModel;
    private $creditoModel;
    private $historicoCreditoModel;
    private $notGwPagModel;

    public function __construct(
        Fatura $faturaModel,
        CreditoUser $creditoModel,
        HistoricoCredito $historicoCreditoModel,
        NotificacaoGatewayPagamento $notGwPagModel
    ) {
        $this->faturaModel =  $faturaModel;
        $this->creditoModel = $creditoModel;
        $this->historicoCreditoModel = $historicoCreditoModel;
        $this->notGwPagModel = $notGwPagModel;
    }
    
    public function webHookResponse(Request $request){
        $dados = $request->all();
        $num_doc = $dados['order_nsu'];
        $transaction_id = $dados['transaction_nsu'];
        
        $notificacao = [
                'metodo_origem' => 'InfinitePayWebHookController::index',
                'transacao_id' => null,
                'conteudo_notificacao' => json_encode($dados,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];

        $notificacao['transacao_id'] = $transaction_id;
        $this->notGwPagModel->create($notificacao);
        
        $notificaoExiste = $this->notGwPagModel->where('transacao_id', $transaction_id)
            ->orderBy('id', 'DESC')
            ->first();

        $fatura = $this->faturaModel->with(['empresa'])
            ->where('fatura_status_id', 1)
            ->where('num_doc', $num_doc)
            ->first();

        if (!is_null($fatura)) {
            $faturaQtdItens = $fatura->items()->count();
            
            if ($faturaQtdItens == 1) {
                $l = License::where('empresa_id', $fatura->empresa_id)
                    //->whereDate('validate', '>=', DB::raw('CURDATE()'))
                    ->orderBy('id', 'DESC')
                    ->first();

                $variacaoPlano = $fatura->items()->first()->variacaoPlano()->first();

                $estaVencido = Carbon::parse($l->validate)->isPast();
                
                if ($estaVencido) {
                    $dataRenovacao = Carbon::now()->format('Y-m-d');
                    $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                        ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                        ->format('Y-m-d');
                } else {
                    $dataRenovacao = $l->validate;
                    $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                        ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                        ->format('Y-m-d');
                }     

                $l->validate = $dataVencimento;
                $l->save();

                $fatura->fatura_status_id = 3; 
                $fatura->transacao_id = $transaction_id;
                $fatura->save();

                $notificaoExiste->processado = 'S';
                $notificaoExiste->save();
            }else{
                //inserir crédito somente
                $credito = $this->creditoModel
                    ->where('user_id', $fatura->user_id)
                    ->first();

                $credito->credito += $fatura->total;

                $this->historicoCreditoModel->create([
                    'user_id' => $credito->user_id,
                    'valor' => $fatura->total,
                    'operacao' => 'A' //ADIÇÃO DE CRÉDITO
                ]);

                $credito->save();

                $fatura->fatura_status_id = 3; //paga
                $fatura->transacao_id = $transaction_id;
                $fatura->save();
            }
        }
    }
}
