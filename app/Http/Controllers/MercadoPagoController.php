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
use Illuminate\Support\Facades\Log;
use stdClass;

class MercadoPagoController extends Controller
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

    public function index()
    {
        $dados = request()->all();
        
        try{
            $notificacao = [
                    'metodo_origem' => 'MercadoPagoController::index',
                    'transacao_id' => null,
                    'conteudo_notificacao' => json_encode($dados,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ];

            //Utilitarios::sendMessage("MercadoPagoController::index - Chegou Notificação");

            if (isset($dados['topic']) && $dados['topic'] == 'payment') {
                /*$transacaoId = $dados['id'];
                $notificacao['transacao_id'] = $transacaoId;
                $this->processamentoFatura($transacaoId);*/
            }else{
                $transacaoId = $dados['data']['id'];
                $notificacao['transacao_id'] = $transacaoId;
                $this->notGwPagModel->create($notificacao);
                //$this->processamentoFatura($transacaoId);

                $notificaoExiste = $this->notGwPagModel->where('transacao_id', $dados['data']['id'])
                    ->orderBy('id', 'DESC')
                    ->first();

                $fatura = $this->faturaModel
                    ->where('fatura_status_id', 1)
                    ->where('transacao_id', $notificaoExiste->transacao_id)
                    ->first();

                if (!is_null($fatura)) {
                    $faturaQtdItens = $fatura->items()->count();
                    $retorno = $this->consultar($notificaoExiste->transacao_id);

                    //verificar se há licença se não houver adicionar
                    $this->verificarSeLicenca($fatura->empresa_id);

                    if($retorno->status == 'approved'){
                        if ($faturaQtdItens == 1) {
                            $l = $fatura->empresa()->first()
                                ->licenca()
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
                                //Utilitarios::sendMessage('Vencida - Licença da empresa: ' . $fatura->empresa()->first()->razao_social . ' Plano '. $variacaoPlano->descricao  .' foi de ' . $l->validate . ' para ' . $dataVencimento);
                            } else {
                                $dataRenovacao = $l->validate;
                                $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                                    ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                                    ->format('Y-m-d');

                                //Utilitarios::sendMessage('Não Vencida - Licença da empresa: ' . $fatura->empresa()->first()->razao_social . ' Plano '. $variacaoPlano->descricao  .' foi de ' . $l->validate . ' para ' . $dataVencimento);
                            }     

                            $l->validate = $dataVencimento;
                            $l->save();

                            $fatura->fatura_status_id = 3; 
                            $fatura->save();

                            $notificaoExiste->processado = 'S';
                            $notificaoExiste->save();
                        }
                    }
                }
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            Log::info($dados);
            exit;
        }
    }

    public function checarPagamento($transacaoId){
        $retorno = new stdClass();
        $retorno->status = false;

        try{ 
            //$retorno = $this->processamentoFatura($transacaoId);
            //$retorno = $this->consultar($transacaoId);
            $fatura = $this->faturaModel
                ->where('fatura_status_id', 1)
                ->where('transacao_id', $transacaoId)
                ->first();

            if(!is_null($fatura)){
                //$retorno = $this->consultar($transacaoId);
            }else{
                $retorno->status = 'approved';
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            //Utilitarios::sendMessage('Erro' . $e->getMessage());
            exit;
        }

        return response()->json([
            'success' => true,
            'payment_status' => ($retorno->status == 'approved') ? true : false,
        ]);
    }

    public function webHookResponse()
    {
        $dados = request()->all();
    
        //Utilitarios::sendMessage('MercadoPagoController::webHookResponse');

        $notificacao = [
                'metodo_origem' => 'MercadoPagoController::webHookResponse',
                'transacao_id' => null,
                'conteudo_notificacao' => json_encode($dados,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];

        $paymentUpdate = $dados['action'] == 'payment.updated' ? true : false;
        
        if($paymentUpdate){            
            $notificacao['transacao_id'] = $dados['data']['id'];
            
            $this->notGwPagModel->create($notificacao);
            $notificaoExiste = $this->notGwPagModel->where('transacao_id', $dados['data']['id'])
                ->orderBy('id', 'DESC')
                ->first();

            $fatura = $this->faturaModel
                ->where('fatura_status_id', 1)
                ->where('transacao_id', $notificaoExiste->transacao_id)
                ->first();

            if (!is_null($fatura)) {
                //verificar se há licença se não houver adicionar
                $this->verificarSeLicenca($fatura->empresa_id);

                $faturaQtdItens = $fatura->items()->count();
                $retorno = $this->consultar($notificaoExiste->transacao_id);

                if ($faturaQtdItens == 1) {
                    if ($retorno->status == 'approved') {
                        
                        $l = $fatura->empresa()->first()
                            ->licenca()
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
                            //Utilitarios::sendMessage('Vencida - Licença da empresa: ' . $fatura->empresa()->first()->razao_social . ' Plano '. $variacaoPlano->descricao  .' foi de ' . $l->validate_pt_br . ' para ' . $dataVencimento);
                        } else {
                            $dataRenovacao = $l->validate;
                            $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                                ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                                ->format('Y-m-d');

                            //Utilitarios::sendMessage('Não Vencida - Licença da empresa: ' . $fatura->empresa()->first()->razao_social . ' Plano '. $variacaoPlano->descricao  .' foi de ' . $l->validate_pt_br . ' para ' . $dataVencimento);
                        }     

                        $l->validate = $dataVencimento;
                        $l->save();

                        $fatura->fatura_status_id = 3; 
                        $fatura->save();

                        $notificaoExiste->processado = 'S';
                        $notificaoExiste->save();
                    }
                }else{
                    if ($retorno->status == 'approved' && $fatura->fatura_status_id == 1) {
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
                        $fatura->save();

                        Utilitarios::sendMessage('Cliente do Portal Nota Fácil ' . $fatura->user()->first()->name . ' pagou via PIX a inserção de Crédito.');
                    }
                }            
            }else{
                //SEM FATURA NÃO PRECISA IMPLEMENTAR AINDA - 02/03/2026
            }
        }        
    }


    /* Este método apenas consulta na api do mercado pago */
    protected function consultar($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/v1/payments/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . getenv('MERCADOPAGO_ACCESS_TOKEN');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        //curl_close($ch);

        $result = json_decode($result);

        return $result;
    }

    protected function verificarSeLicenca($empresa_id){
        $check = License::where('empresa_id', $empresa_id)->first();

        if(is_null($check)){
            License::create([
                'empresa_id' => $empresa_id,
                'validate' => Carbon::now()->subDay(1)->format('Y-m-d')
            ]);            
        }/*else{
            Utilitarios::sendMessage('A empresa ID: # '. $empresa_id . ' não possuia licença cadastrada.');
        }*/
    }
}
