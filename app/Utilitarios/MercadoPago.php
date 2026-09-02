<?php
namespace App\Utilitarios;

use Illuminate\Support\Facades\Config;
use Laracasts\Flash\Flash;

class MercadoPago
{
    private $mp;

    public function __construct(){
        $this->mp = new MP (env("MP_APP_ACCESS_TOKEN"));
        $this->mp->sandbox_mode(FALSE);
    }

    public function checkout($user, $fatura)
    {
        $plano = $fatura->items()->first()->plano()->first();
        $varPlano = $fatura->items()->first()->variacaoPlano()->first();
        $referencia[] = $fatura->num_doc;
        $referencia[] = 'PLAN' . $plano->id . 'VAR' . $varPlano->id;
        $descricao = $plano->plano_nome . ' / ' . $varPlano->descricao;
        $valorFatura = (float)$varPlano->valor;

        $preference_data = array (
            "notification_url"=> "https://https://nfse-nacional.portalnotafacil.com.br/retorno/mercadopago",
            "description"=> $descricao, //DESCRIÇÃO DO CARRINHO OU ITEM VENDIDO.
            "transaction_amount"=> $valorFatura, //VALOR TOTAL A SER PAGO PELO COMPRADOR.
            "external_reference"=> $referencia[0], //NUMERO DO PEDIDO DE SEU SITE PARA FUTURA CONCILIAÇÃO FINANCEIRA.
            "payer"=> array( //DADOS ESSENCIAIS PARA REGISTRO DO BOLETO
                "email"=> $user->email, //EMAIL DO COMPRADOR
                "first_name"=> $user->name, //PRIMEIRO NOME DO COMPRADOR
                "last_name"=> "", //SOBRENOME DO COMPRADOR, OPCIONAL SE FOR PESSOA JURIDICA
                "identification"=> array( //DADOS DE IDENTIFICAÇÃO DO COMPRADOR
                    "type"=> "CPF", //TIPO DE DOCUMENTO, CPF OU CNPJ CASO BRASIL
                    "number"=> $user->cpf //NUMERAÇÃO DO DOCUMENTO INFORMADO
                ),
                "address"=>  array( //ENDEREÇO DO COMPRADOR
                    "zip_code"=> $user->cepf, //CEP DO COMPRADOR
                    "street_name"=> $user->logradouro, //RUA DO COMPRADOR
                    "street_number"=> $user->numero, //NÚMERO DO COMPRADOR
                    "neighborhood"=> $user->bairro, //BAIRRO DO COMPRADOR
                    "city"=> $user->cidade->municipio, //CIDADE DO COMPRADOR
                    "federal_unit"=> $user->cidade->estado->sigla //UNIDADE FEDERATIVA RESUMIDA EM SIGLA DO COMPRADOR
                )
            ),
            "items" => array (
                array (
                    "title" => $descricao,
                    "quantity" => 1,
                    "currency_id" => "BRL",
                    "unit_price" => $valorFatura
                )
            )
        );

        $preference = $this->mp->create_preference($preference_data);
        if($preference['status'] == 201){
            //$url = $preference['response']['init_point'];
            $url = $preference['response']['sandbox_init_point'];
        }else{
            Flash::error('Não foi possível gerar a sua Fatura. Por favor Tente Novamente!');
            return redirect()->back();
        }

        return array('erro' => 0, 'msg' => $url);
    }
}