<?php

namespace Hadder\NfseNacional;

use DOMDocument;
use NFePHP\Common\Certificate;

class Tools extends RestCurl
{
    public function __construct(string $config, Certificate $cert)
    {
        parent::__construct($config, $cert);
    }

    public function consultaDocumentosFiscaisServico(string $nsu, string $cnpj){
        $operacao = 'contribuintes/DFe/' . $nsu .'?cnpjConsulta='.$cnpj .'&lote=true';
        
        $retorno = $this->fazerRequisicao($operacao, null, 2);

        if (isset($retorno['erro'])) {
            return $retorno;
        }

        if ($retorno) {
            /*$base_decode = base64_decode($retorno['nfseXmlGZipB64'], true);

            if ($base_decode === false) {
                return ['erro' => 'Falha ao decodificar o Base64.'];
            }

            $gz_decode = gzdecode($base_decode);

            if ($gz_decode === false) {
                return ['erro' => 'Falha ao descompactar o XML GZip.'];
            }

            // Retorna o XML em UTF-8, sem conversão desnecessária.
            return $gz_decode;*/

            if(isset($retorno['StatusProcessamento'])){
                foreach($retorno['LoteDFe'] as $key => $data){
                    $base_decode = base64_decode($data['ArquivoXml'], true);
                    if ($base_decode === false) {
                        $retorno['LoteDFe'][$key]['xml_status'] = 'Falha ao decodificar o Base64.';
                    }
                    
                    $gz_decode = gzdecode($base_decode);

                    if ($gz_decode === false) {
                        dd('Falha ao descompactar o XML GZip.');
                    }
                    $retorno['LoteDFe'][$key]['ConteudoXml'] = $gz_decode;
                }
            }

            return $retorno;
        }
        return null;
    }

    public function consultarNfseChave($chave)
    {
        $operacao = 'nfse/' . $chave;
        $retorno = $this->getData($operacao);

        if (isset($retorno['erro'])) {
            return $retorno;
        }
       
        if ($retorno) {
            $base_decode = base64_decode($retorno['nfseXmlGZipB64'], true);

            if ($base_decode === false) {
                return ['erro' => 'Falha ao decodificar o Base64.'];
            }

            $gz_decode = gzdecode($base_decode);

            if ($gz_decode === false) {
                return ['erro' => 'Falha ao descompactar o XML GZip.'];
            }

            // Retorna o XML em UTF-8, sem conversão desnecessária.
            return $gz_decode;
        }
        return null;
    }

    public function consultarDpsChave($chave)
    {
        $operacao = 'dps/' . $chave;
        $retorno = $this->getData($operacao);

        return $retorno;
    }

    public function consultarNfseEventos($chave, $tipoEvento = null, $nSequencial = null)
    {
        $operacao = 'nfse/' . $chave . '/eventos';
        if ($tipoEvento) {
            $operacao .= '/' . $tipoEvento;
        }
        if ($nSequencial) {
            $operacao .= '/' . $nSequencial;
        }
        $retorno = $this->getData($operacao);
        return $retorno;
    }

    public function consultarDanfse($chave)
    {
        $operacao = 'danfse/' . $chave;
        $retorno = $this->getData($operacao, null, 2);
        if (isset($retorno['erro'])) {
            return $retorno;
        }
        
        if ($retorno) {
            return $retorno;
        }
        return null;
    }

    public function consultarAliquotas(string $codigoMunicipio, string $codigoServico)
    {
        $operacao = 'parametrizacao/' . $codigoMunicipio . '/' . $codigoServico. '/' . date('Y-m-d') . '/aliquota';
        
        $retorno = $this->getData($operacao, null, 2); //Consultar Aliquotas é necessário usar URL de consulta da ADN (origem = 2), 1 seria sefin

        if (isset($retorno['erro'])) {
            return $retorno;
        }

        if ($retorno) {
            return $retorno;
        }
        
        return null;
    }

    public function enviaDps($content, $origem = 1)
    {
        //$content = $this->canonize($content);
        $content = $this->sign($content, 'infDPS', '', 'DPS');
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . $content;

        $gz = gzencode($content);
        $data = base64_encode($gz);
        $dados = [
            'dpsXmlGZipB64' => $data
        ];
        $retorno = $this->postData('nfse', json_encode($dados), $origem);
        return $retorno;
    }

    public function enviaDpsAdn($content, $origem = 1)
    {
        // Assina o XML
        $content = $this->sign($content, 'infDPS', '', 'DPS');
        
        // Adiciona declaração XML
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . $content;

        // 2. Compactar obrigatoriamente usando gzencode() com nível de compressão 9
        // IMPORTANTE: Use gzencode() e não gzcompress(). O SEFIN exige os headers do GZIP.
        $xmlGzip = gzencode($content, 9);

        // 3. Converter o binário resultante para Base64 (sem quebras de linha)
        $xmlBase64 = base64_encode($xmlGzip);

        // 4. Montar o JSON exatamente com a propriedade exigida pelo manual
        $payloadJson = json_encode([
            'dpsXmlGZipB64' => $xmlBase64
        ], JSON_UNESCAPED_SLASHES);

        return $this->postDataAdn('nfse', $payloadJson, $origem);
    }

    public function cancelaNfse($std)
    {
        $dps = new \Hadder\NfseNacional\Dps($std);
        $content = $dps->renderEvento($std);
        //$content = $this->canonize($content);
        $content = $this->sign($content, 'infPedReg', '', 'pedRegEvento');
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . $content;
        $gz = gzencode($content);
        $data = base64_encode($gz);
        $dados = [
            'pedidoRegistroEventoXmlGZipB64' => $data
        ];
        $operacao = 'nfse/' . $std->infPedReg->chNFSe . '/eventos';
        $retorno = $this->postData($operacao, json_encode($dados));
        return $retorno;
    }

    protected function canonize($content)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($content);
        dump($dom->saveXML());
        return $dom->C14N(false, false, null, null);
    }
}