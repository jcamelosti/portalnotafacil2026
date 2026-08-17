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
use Illuminate\Support\Facades\DB;

class Safe2PayController extends Controller
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
    )
    {
        $this->faturaModel =  $faturaModel;
        $this->creditoModel = $creditoModel;
        $this->historicoCreditoModel = $historicoCreditoModel;
        $this->notGwPagModel = $notGwPagModel;
    }

    public function hook(Request $request){
        $data = $request->all();

        if (is_array($data)) {
            $this->notGwPagModel->create([
                'conteudo_notificacao' => json_encode($data,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        }

        //$transactionId = $data['IdTransaction'];
        $transactionStatus = $data['TransactionStatus']['Id'];
        $referencia = $data['Reference'];

        $fatura = $this->faturaModel
            ->where('fatura_status_id', 1)
            ->where('num_doc', $referencia)
            ->first();

        if($transactionStatus == 6){
            Utilitarios::sendMessage('Cliente do Portal Nota Fácil ' . $fatura->user()->first()->name . ' Núm Doc. Ref.: ' . $fatura->num_doc .' solicitou reembolso. Verificar');
        }

        if($fatura) {
            $faturaQtdItens = $fatura->items()->count();

            if ($faturaQtdItens >= 1) {
                if ($transactionStatus == 3 && $fatura->fatura_status_id == 1) {
                    $fatura->fatura_status_id = 3; //paga
                    $fatura->save();
                    $l = $fatura->empresa()->first()
                        ->licenca()->whereDate('validate', '>=', DB::raw('CURDATE()'))
                        ->orderBy('id', 'DESC')
                        ->first();
                    
                    $variacaoPlano = $fatura->items()->first()->variacaoPlano()->first();

                    if (is_null($l)) { //se não há licença valida - se houver licença vencida vai atualizar a partir da data da renovação
                        $dataRenovacao = Carbon::now()->format('Y-m-d');
                        $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                            ->addDay($variacaoPlano->fator_vigencia)->subDay(1)
                            ->format('Y-m-d');
                        
                        $l = License::where('empresa_id', $fatura->empresa()->first()->id)
                            ->first();
                        $l->validate = $dataVencimento;
                        $l->save();                       
                    } else {//se há licença ainda vigente, ou seja, ainda não venceu, pegar a data final da licença e somar dias.
                        $dataRenovacao = $l->validate;
                        $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                            ->addDay($variacaoPlano->fator_vigencia)->subDay(1)
                            ->format('Y-m-d');

                        //parando de adicionar novas licenças -> alterando a vencida.
                        $l->validate = $dataVencimento;
                        $l->save();
                    }

                    Utilitarios::sendMessage('Cliente do Portal Nota Fácil ' . $fatura->user()->first()->name . ' pagou via PIX a Renovação de Licença.');
                }
            } else {
                if ($transactionStatus == 3 && $fatura->fatura_status_id == 1) {
                    //inserir crédito somente
                    $credito = $this->creditoModel
                        ->where('user_id', $fatura->user_id)
                        ->first();

                    $credito->credito += $fatura->total;

                    $this->historicoCreditoModel->create([
                        'user_id' => $credito->user_id,
                        'valor' => $fatura->total,
                        'operacao' => 'A'
                    ]);

                    $credito->save();
                    
                    $fatura->fatura_status_id = 3; //paga
                    $fatura->save();

                    Utilitarios::sendMessage('Cliente do Portal Nota Fácil ' . $fatura->user()->first()->name . ' pagou via PIX a inserção de Crédito.');
                }
            }
        }
    }
}
