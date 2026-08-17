<?php
namespace App\Utilitarios;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


use DOMDocument;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SoapClient;
use SimpleXMLElement;
use Illuminate\Support\Facades\Log;

class Utilitarios
{
    public static function formatar ($tipo, $string, $size = 10)
    {
        switch ($tipo)
        {
            case 'fone':
                if($size === 10){
                    $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 4)
                        . '-' . substr($tipo, 6);
                }else
                    if($size === 11){
                        $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 5)
                            . '-' . substr($tipo, 7);
                    }
                break;
            case 'cep':
                $string = substr($string, 0, 5) . '-' . substr($string, 5, 3);
                break;
            case 'cpf':
                $string = substr($string, 0, 3) . '.' . substr($string, 3, 3) .
                    '.' . substr($string, 6, 3) . '-' . substr($string, 9, 2);
                break;
            case 'cnpj':
                $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                    '.' . substr($string, 5, 3) . '/' .
                    substr($string, 8, 4) . '-' . substr($string, 12, 2);
                break;
            case 'rg':
                $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                    '.' . substr($string, 5, 3);
                break;
            default:
                $string = 'É ncessário definir um tipo(fone, cep, cpg, cnpj, rg)';
                break;
        }
        return $string;
    }

    public static function extrairTagToSlug($node){
        $arr = preg_split('/(?=[A-Z])/', $node);
        $arr = array_filter($arr);

        return Str::slug(implode(' ',$arr), '_');
    }

    public static function limparCpfCnpj(string $valor){
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }

    public static function formatarValoresDB($dados)
    {
        $valoresDecimais = [
            'txtTotal',
            'txtTotalISSQN',
            'txtPis',
            'txtCofins',
            'txtOutros',
            'txtIRRF',
            'txtCSLL',
            'txtISSQNResponsavel',
            'txtOutrasRetencoes',
            'txtValorDescontos',
            'txtTotalRetencoes',
            'txtTotalValorLiquido',
            'txtAliq',
            'txtDeducaoBaseCalculo',
            'txtDescontoInCondicionado',
            'txtDescontoCondicionado'
        ];

        $semPontuacao = [
            'txtCep',
            'txtTelefone'
        ];

        foreach ($dados as $key => $item) {
            if (in_array($key, $valoresDecimais)) {
                if ($item != null) {
                    $dados[$key] = (float)str_replace(',', '.', str_replace('.', '', $item));
                } elseif ($item == null) {
                    $dados[$key] = (float) 0.00;
                } else {
                    $dados[$key] = (float) 0.00;
                }
            }
        }

        if(isset($dados['txtCpfCnpj'])){
            $dados['txtCpfCnpj'] = self::removerPontuacao($dados['txtCpfCnpj']);
        }
        unset($dados['txtTotal2']);
        
        return $dados;
    }

    public static function formatarValoresNota($dados)
    {
        $valoresDecimais = [
            "aliquota",
            "valor_total",
            "valor_deducao_base_calculo",
            "valor_pis",
            "valor_cofins",
            "valor_csll",
            "valor_irrf",
            "valor_outros",
            "valor_outras_retencoes",
            "valor_desconto_condicionado",
            "valor_desconto_incondicionado",
        ];

        $semPontuacao = [
            'cep',
            'telefone',
            "cpf_cnpj"
        ];

        foreach ($dados as $key => $item) {
            /*dd($item);
            if (in_array($key, $valoresDecimais)) {
                if ($item != null) {
                    $dados[$key] = (float)str_replace(',', '.', str_replace('.', '', $item));
                } elseif ($item == null) {
                    $dados[$key] = (float) 0.00;
                } else {
                    $dados[$key] = (float) 0.00;
                }
            }*/

            if($key == 'tomador'){
                foreach ($dados['tomador']['endereco'] as $key1 => $item1) {
                    if (in_array($key1, $semPontuacao)) {
                        $dados['tomador']['endereco'][$key1] = self::removerPontuacao($dados['tomador']['endereco'][$key1]);
                    }
                }

                foreach ($dados['tomador'] as $key11 => $item11) {
                    if (in_array($key1, $semPontuacao)) {
                        $dados['tomador'][$key11] = self::removerPontuacao($item11);
                    }
                }
            }

            if($key == 'dados_nota'){
                foreach ($dados['dados_nota'] as $key2 => $item2) {
                    if (in_array($key2, $valoresDecimais)) {
                        if ($item != null) {
                            $dados['dados_nota'][$key2] = (float)str_replace(',', '.', str_replace('.', '', $item2));
                        } elseif ($item2 == null) {
                            $dados['dados_nota'][$key2] = (float) 0.00;
                        } else {
                            $dados['dados_nota'][$key2] = (float) 0.00;
                        }
                    }
                }
            }
        }

        /*if(isset($dados['txtCpfCnpj'])){
            $dados['txtCpfCnpj'] = self::removerPontuacao($dados['txtCpfCnpj']);
        }
        unset($dados['txtTotal2']);*/        
        return $dados;
    }

    public static function removerPontuacao($item)
    {
        $item = str_replace('-', '', $item);
        $item = str_replace('(', '', $item);
        $item = str_replace(')', '', $item);
        $item = str_replace('/', '', $item);
        $item = str_replace('.', '', $item);
        return $item;
    }

    public static function consultarEmpresaCNPJ($cnpj){
        $headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', '8154ea80438567b4b56877a297a7877baada70f54063d54c7780a68fa8caa733')
        );

        try{
            $curl = curl_init("https://www.receitaws.com.br/v1/cnpj/" . str_replace(['.', '-', '/'], '', $cnpj));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $resposta = curl_exec($curl);
            $result = json_decode($resposta);

            $empresa['cpf_cnpj'] = $cnpj;
            $empresa['razao_social'] = !empty($result->nome) ? $result->nome : '';
            $empresa['nome_fantasia'] = !empty($result->fantasia) ? $result->fantasia : '';
            $empresa['email'] = !empty($result->email) ? strtolower($result->email) : '';
            $empresa['telefone1'] = !empty($result->telefone) ? $result->telefone : '  ';;
            $empresa['cep'] = !empty($result->cep) ? str_replace(['.'], '', $result->cep) : '';
            $empresa['logradouro'] = !empty($result->logradouro) ? $result->logradouro : '';
            $empresa['bairro'] = !empty($result->bairro) ? $result->bairro : '';
            $empresa['complemento'] = !empty($result->complemento) ? $result->complemento : '';
            $empresa['numero'] = !empty($result->numero) ? $result->numero : 'S/N';
            $empresa['municipio'] = $result->municipio;
            $empresa['uf'] = $result->uf;
            $empresa['porte'] = $result->porte;
            $empresa['natureza_juridica'] = $result->natureza_juridica;
        
            return $empresa;
        }catch(\Exception $e){
            dd($e->getMessage());
            return [];
        }        
    }

    public static function sendMessage($messagem) {
        $token = "8269207890:AAHrjuGzKPjlWMrEjl0NiwaNe4WecMTnpcA";
        $chatid = "7011003487";

        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatid;
        $url = $url . "&text=" . urlencode($messagem);
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function proximoDiaUtil($data, $saida = 'd/m/Y') {
        $timestamp = strtotime($data);
        $dia = date('N', $timestamp);

        if ($dia >= 6) {
            $timestamp_final = $timestamp + ((8 - $dia) * 3600 * 24);
        } else {
            $timestamp_final = $timestamp;
        }

        return date($saida, $timestamp_final);
    }

    public static function somenteLetrasENumerosSemSimbolos($str){
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
        $nova_string = str_replace($comAcentos, $semAcentos, $str);
        return $nova_string;                    
    }


    public static function arredondarSispetro($valor){
        $valorRetorno = '';
                    
        $parte = explode('.', $valor);
        $parteFracao = isset($parte[1]) ? $parte[1]: 0;
       
        $strLen = strlen($parteFracao);
        
        //regra 1
        if($strLen == 3){
            if((int)$parteFracao[2] <= 5){
                $valorRetorno = substr($valor, 0, -1);
            }
        }
        
        //regra 2
        if($strLen == 4){
            if((int)$parteFracao[2] >= 5 || (int)$parteFracao[3] != 0){
                $valorRetorno = round($valor,2);
            }
        }
        
        //regra 3
        if($strLen == 3){		
            if(( (int)$parteFracao[1] % 2 == 1 ) && (int)$parteFracao[2] >= 5){
                $valorRetorno = round($valor,2);
            }
        }
        
        //regra 4
        if($strLen == 3){			
            if(( (int)$parteFracao[1] % 2 == 0 ) && (int)$parteFracao[2] == 5){
                $valorRetorno = substr($valor, 0, -1);
            }
        }
        
        //regra 
        if($strLen == 4){		
            if(( (int)$parteFracao[1] % 2 == 1 ) && (int)$parteFracao[2] == 5){
                $valorRetorno = round($valor,2);
            }
        }
        
        //regra 
        if($strLen == 4){		
            if(( (int)$parteFracao[1] % 2 == 1 ) && (int)$parteFracao[2] == 5 && (int)$parteFracao[3] != 0){
                $valorRetorno = round($valor,2);
            }
        }
        
        if($strLen == 3 && $valorRetorno == ''){	
            if((int)$parteFracao[1] >= 5 && isset($parteFracao[2]) && $parteFracao[2] > 0){
                $valorRetorno = round($valor,1);
            }else{
                $valorRetorno = round($valor,2);
            }
        }	

        if($valorRetorno == ''){
            $valorRetorno = $valor;
        }
                
        return $valorRetorno;
    }
}
