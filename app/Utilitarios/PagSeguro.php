<?php
namespace App\Utilitarios;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class PagSeguro
{
    private $configPagSeguro;

    public function __construct(){
        $this->configPagSeguro = Config::get('pagseguro');
    }

    public function checkoutInscricao($user, $fatura)
    {
        if ($this->configPagSeguro['environment'] == 'sandbox') {
            //'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout'
        }else{
            $url = "https://ws.pagseguro.uol.com.br/v2/checkout/?email=" .$this->configPagSeguro['email'] ."&token=".$this->configPagSeguro['token'];
        }

        $plano = $fatura->items()->first()->plano()->first();
        $varPlano = $fatura->items()->first()->variacaoPlano()->first();



        //Dados da compra
        /*$dadosCompra['currency'] = "BRL";
        $dadosCompra['itemId1'] = 'PLAN' . $plano->id . 'VAR' . $varPlano->id;
        $dadosCompra['itemDescription1'] = substr($plano->plano_nome . ' / ' . $varPlano->descricao,0, 99); //limite de 100 digitos
        $dadosCompra['itemAmount1'] = $varPlano->valor;
        $dadosCompra['itemQuantity1'] = 1;*/

        # Instancia do objeto XMLWriter
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        $res = xmlwriter_set_indent_string($xw, ' ');
        xmlwriter_start_document($xw, '1.0', 'ISO-8859-1');

        xmlwriter_start_element($xw, 'checkout');

        //currency
        xmlwriter_start_element($xw, 'currency');
        xmlwriter_text($xw, 'BRL');
        xmlwriter_end_element($xw);

        //items
        xmlwriter_start_element($xw, 'items');
        xmlwriter_start_element($xw, 'item');

        xmlwriter_start_element($xw, 'id');
        xmlwriter_text($xw, 'PLAN' . $plano->id . 'VAR' . $varPlano->id);
        xmlwriter_end_element($xw);//id

        xmlwriter_start_element($xw, 'description');
        xmlwriter_text($xw, substr($plano->plano_nome . ' / ' . $varPlano->descricao,0, 99));
        xmlwriter_end_element($xw);//description

        xmlwriter_start_element($xw, 'amount');
        xmlwriter_text($xw, $varPlano->valor);
        xmlwriter_end_element($xw);//amount

        xmlwriter_start_element($xw, 'quantity');
        xmlwriter_text($xw, '1');
        xmlwriter_end_element($xw);//quantity

        xmlwriter_end_element($xw);//item
        xmlwriter_end_element($xw);//items
        //final items

        //referencia
        xmlwriter_start_element($xw, 'reference');
        xmlwriter_text($xw, $fatura->num_doc);
        xmlwriter_end_element($xw);


        //final do doc xml
        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);
        $xml = xmlwriter_output_memory($xw, 0);

        //Transformando os dados da compra no formato da URL
        //$dadosCompra = http_build_query($dadosCompra);

        //Realizando a chamada
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_POSTFIELDS, $dadosCompra);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Content-Type: application/xml; charset=ISO-8859-1'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        $respostaPagSeguro = curl_exec($curl);
        //$http = curl_getinfo($curl);

        $respostaPagSeguro= simplexml_load_string($respostaPagSeguro);
        $json = json_encode($respostaPagSeguro);
        $retorno = json_decode($json,TRUE);

        if (!empty($retorno['error'])) {
            if (isset($retorno['error']['message'])) {
                $msg = $retorno['error']['message'];
            } else {
                $msg = $retorno['error']['message'];
            }
            return array('erro' => 1, 'msg' => $msg);
        }

        if ($this->configPagSeguro['environment'] == 'sandbox') {
            $url = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $retorno['code'];
        } else {
            $url = 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $retorno['code']; //oficial
        }

        return array('erro' => 0, 'msg' => $url);
    }

    public function mudancaStatus($request){
        if(isset($request['notificationCode'])){
            $notificacao = $request['notificationCode'];

            if ($this->configPagSeguro['environment'] == 'sandbox') {
                //sand box
                $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/".$notificacao;
                $url .= '?email=' . $this->configPagSeguro['email'] . '&token=' . $this->configPagSeguro['token'];
            } else {
                //oficial
                $url = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/".$notificacao;
                $url .= '?email=' . $this->configPagSeguro['email'] . '&token=' . $this->configPagSeguro['token'];
            }

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded; charset=UTF-8"]);
            $retorno = curl_exec($curl);
            $http = curl_getinfo($curl);

            if($retorno == 'Unauthorized'){
                print_r($retorno);
                exit;
            }
            curl_close($curl);

            $retorno = simplexml_load_string($retorno);
            $json = json_encode($retorno);
            $retorno = json_decode($json,TRUE);
            $respTransacao = @json_decode(@json_encode($retorno),1);
            $transacao_id = str_replace('-', '', $respTransacao['code']);
            $respTransacaoTratada = $this->getDadosPagamento($respTransacao);

            return ['transacao_id' => $transacao_id, 'dados_transacao' => $respTransacaoTratada];
        }

        return null;
    }

    public function getInfoConfPagto($transaction_id){
        if ($this->configPagSeguro['environment'] == 'sandbox') {
            //sand box
            $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/".$transaction_id;
            $url .= '?email=' . $this->configPagSeguro['email'] . '&token=' . $this->configPagSeguro['token'];
        } else {
            //oficial
            $url = "https://ws.pagseguro.uol.com.br/v3/transactions/".$transaction_id;
            $url .= '?email=' . $this->configPagSeguro['email'] . '&token=' . $this->configPagSeguro['token'];
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded; charset=UTF-8"]);
        $retorno = curl_exec($curl);
        $http = curl_getinfo($curl);

        if($retorno == 'Unauthorized'){
            print_r($retorno);
            exit;
        }
        curl_close($curl);

        $retorno = simplexml_load_string($retorno);
        $json = json_encode($retorno);
        $retorno = json_decode($json,TRUE);
        $respTransacao = @json_decode(@json_encode($retorno),1);
        $transacao_id = str_replace('-', '', $respTransacao['code']);
        $respTransacaoTratada = $this->getDadosPagamento($respTransacao);

        return ['transacao_id' => $transacao_id, 'dados_transacao' => $respTransacaoTratada];
    }

    public function obterStatusTransacao($codigo, $tipoRetorno = '') {
        switch($codigo) {
            case 1:
                $statusRetorno = array('id' => 1, 'descricao' => 'Aguardando pagamento');
                break;
            case 2:
                $statusRetorno = array('id' => 2, 'descricao' => 'Em análise');
                break;
            case 3:
                $statusRetorno = array('id' => 3, 'descricao' => 'Paga');
                break;
            case 4:
                $statusRetorno = array('id' => 4, 'descricao' => 'Disponível');
                break;
            case 5:
                $statusRetorno = array('id' => 5, 'descricao' => 'Em disputa');
                break;
            case 6:
                $statusRetorno = array('id' => 6, 'descricao' => 'Devolvida');
                break;
            case 7:
                $statusRetorno = array('id' => 7, 'descricao' => 'Cancelada');
                break;
            default:
                $statusRetorno = array('id' => 0, 'descricao' => 'Não foi possível obter o status');
                break;
        }

        if (is_array($tipoRetorno)) {
            return $statusRetorno;
        } else {
            return $statusRetorno['descricao'];
        }
    }

    public function obterTipoPagamento($codigo) {
        switch($codigo) {
            case 1:
                $tipo = 'Cartão de crédito';
                break;
            case 2:
                $tipo = 'Boleto';
                break;
            case 3:
                $tipo = 'Débito online (TEF)';
                break;
            case 4:
                $tipo = 'Saldo PagSeguro';
                break;
            case 5:
                $tipo = 'Oi Paggo';
                break;
            default:
                $tipo = 'Informação não disponível';
                break;
        }
        return $tipo;
    }

    public function obterMeioPagamento($codigo) {
        switch($codigo) {
            case 101:
                $meio = 'Cartão de crédito Visa';
                break;
            case 102:
                $meio = 'Cartão de crédito MasterCard';
                break;
            case 103:
                $meio = 'Cartão de crédito American Express';
                break;
            case 104:
                $meio = 'Cartão de crédito Dinners';
                break;
            case 105:
                $meio = 'Cartão de crédito Hypercard';
                break;
            case 106:
                $meio = 'Cartão de crédito Aura';
                break;
            case 107:
                $meio = 'Cartão de crédito Elo';
                break;
            case 108:
                $meio = 'Cartão de crédito PLENOCard';
                break;
            case 109:
                $meio = 'Cartão de crédito PersonalCard';
                break;
            case 110:
                $meio = 'Cartão de crédito JCB';
                break;
            case 111:
                $meio = 'Cartão de crédito Discover';
                break;
            case 112:
                $meio = 'Cartão de crédito BrasilCard';
                break;
            case 113:
                $meio = 'Cartão de crédito FORTBRASIL';
                break;
            case 202:
                $meio = 'Boleto Santander';
                break;
            case 301:
                $meio = 'Débito Online Bradesco';
                break;
            case 302:
                $meio = 'Débito Online Itaú';
                break;
            case 304:
                $meio = 'Débito Online Banco do Brasil';
                break;
            case 306:
                $meio = 'Débito Online Banrisul';
                break;
            case 307:
                $meio = 'Débito Online HSBC';
                break;
            case 401:
                $meio = 'Saldo PagSeguro';
                break;
            case 501:
                $meio = 'Oi Paggo';
                break;
        }
        return $meio;
    }


    public function obterTipoTransacao($codigo) {
        switch ($codigo) {
            case 1:
                $tipo = 'Pagamento';
                break;
            case 2:
                $tipo = 'Transferência';
                break;
            case 3:
                $tipo = 'Adição de fundos'; // confirmar
                break;
            case 4:
                $tipo = 'Saque';
                break;
            case 5:
                $tipo = 'Recarga'; // confirmar
                break;
            case 6:
                $tipo = 'Doação';
                break;
            case 7:
                $tipo = 'Bônus';
                break;
            case 8:
                $tipo = 'Repasse de bônus'; // confirmar
                break;
            case 9:
                $tipo = 'Operacional'; // confirmar
                break;
            case 10:
                $tipo = 'Doação política';
                break;
            default:
                $tipo = 'Não especificado';
                break;
        }
        return $tipo;
    }

    public function getDadosPagamento($data){
        return [
            'referencia' => $data['reference'],
            'data_transac' => substr($data['date'],0,10),
            'tipo' => [
                $data['type'],
                $this->obterTipoTransacao($data['type'])
            ],
            'status' => [
                $data['status'],
                $this->obterStatusTransacao($data['status'])
            ],
            'forma-pagamento' => [
                $data['paymentMethod']['type'],
                $this->obterMeioPagamento($data['paymentMethod']['code'])
            ],
        ];
    }
}