<?php namespace App\Traits;

use App\Models\EndPoint;
use Illuminate\Support\Facades\Session;
use App\Models\Certificado as CertificadoEmpresa;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificado;
use NFePHP\NFSe\Models\Issnet\RenderRPS;
use NFePHP\Common\DOMImproved as Dom;
use SoapClient;
use stdClass;

trait IssnetTrait
{
    protected function tipoEmpresaTag($cpfCnpj){
        $model = new Empresa();
        return $model->getTipoPessoa($cpfCnpj) == 2 ? '<Cnpj>'.$cpfCnpj.'</Cnpj>' : '<Cpf>'.$cpfCnpj.'</Cpf>';
    }
    /*
        Consultar Url de Visualização da NFSe
    */
    protected function consultarUrlNota($nota, $empresa = null){
        if(is_null($empresa)){
            $empresaSessao = Session::get('empresa_selecionada');
            $empresaSessao = Empresa::find($empresaSessao);
        }else{
            $empresaSessao = $empresa;
        }
        
        $xml='
            <ConsultarUrlNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
                <Pedido>
                    <Prestador>
                        <CpfCnpj>
                            '. $this->tipoEmpresaTag($empresaSessao->cpf_cnpj) .'
                        </CpfCnpj>
                        <InscricaoMunicipal>'.$empresaSessao->inscricao_municipal.'</InscricaoMunicipal>
                    </Prestador>
                    <NumeroNfse>'.$nota->num_nfse.'</NumeroNfse>
                    <Pagina>1</Pagina>
                </Pedido>
            </ConsultarUrlNfseEnvio>
        ';

        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'ConsultarUrlNfseEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));
        $xml = ltrim($xml);

        if(is_null($empresa)){
            $retorno = $this->enviarRequisicao($xml, 'ConsultarUrlNfse');
        }else{
            $retorno = $this->sendRequest($xml, 'ConsultarUrlNfse', $empresa);
        }

        return (string)$retorno
            ->ConsultarUrlNfseResponse
            ->ConsultarUrlNfseResposta
            ->ListaLinks
            ->Links
            ->UrlVisualizacaoNfse[0];
    }


    /*
        Consultar Url de Visualização da NFSe
    */
    protected function consultarUrlNotaApi($nota, $empresa = null){
        $empresaSessao = $empresa;

        $xml='
            <ConsultarUrlNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
                <Pedido>
                    <Prestador>
                        <CpfCnpj>
                            '. $this->tipoEmpresaTag($empresaSessao->cpf_cnpj) .'
                        </CpfCnpj>
                        <InscricaoMunicipal>'.$empresaSessao->inscricao_municipal.'</InscricaoMunicipal>
                    </Prestador>
                    <NumeroNfse>'.$nota->num_nfse.'</NumeroNfse>
                    <Pagina>1</Pagina>
                </Pedido>
            </ConsultarUrlNfseEnvio>
        ';

        $xmlNotaAssinado = $this->assinarRpsRepetidamenteApi($xml, 'ConsultarUrlNfseEnvio', $empresa->cpf_cnpj); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));
        $xml = ltrim($xml);

        $retorno = $this->sendRequest($xml, 'ConsultarUrlNfse', $empresa);
        
        return (string)$retorno
            ->ConsultarUrlNfseResponse
            ->ConsultarUrlNfseResposta
            ->ListaLinks
            ->Links
            ->UrlVisualizacaoNfse[0];
    }

    /*
        Faz Chamada no WebService
    */
    protected function enviarRequisicao($xmlAssinado, $function){
        if(request()->session()->has('empresa_selecionada')){
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = Empresa::find($empresaSessao);
        }else{
            $empresaSessao = request()->session()->get('dados_empresa');
        }

        $endPointUrl = EndPoint::where('codigo_municipio', $empresaSessao->cidade_id)
            ->first();

        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();

        $wsdl       = $endPointUrl->url_endpoint;
        $endPoint   = $endPointUrl->url_endpoint;

        
        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificado = getenv("CAMINHO_CERTIFICADO_LOCAL").$empresaSessao->cpf_cnpj."_certKEY.pem";
        }else{
            $certificado = getenv("CAMINHO_CERTIFICADO_PROD").$empresaSessao->cpf_cnpj."_certKEY.pem";
        }
        $passwordCert = base64_decode($certificadoCliente->senha);
              
        $options = [
            'location' => $endPoint,
            'keep_alive' => false,//true
            'trace' => true,
            'local_cert' => $certificado,
            'passphrase' => $passwordCert,
            'cache_wsdl' => 0,
            'wsdl_cache' => WSDL_CACHE_NONE,
            'exceptions' => false,
            'use' => SOAP_ENCODED,
            //'soap_version' => SOAP_1_2,
            'stream_context'=> stream_context_create([
                'http' => [
                    'protocol_version'=>'1.1',
                    'header' => 'Connection: Close',
                    'user_agent' => 'PHPSoapClient'
                ],
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]),
        ];

        try {
            $client = new \SoapClient($wsdl, $options);

            $dom = new Dom('1.0', 'utf-8');
            $root = $dom->createElement('cabecalho');    

            //versao - atributo do cabecalho
            $domAttribute = $dom->createAttribute('versao');
            $domAttribute->value = '1.00';
            $root->appendChild($domAttribute);

            //xmlns
            $domAttribute = $dom->createAttribute('xmlns');
            $domAttribute->value = 'http://www.abrasf.org.br/nfse.xsd';
            $root->appendChild($domAttribute);

            $dom->addChild(
                $root,
                'versaoDados',
                '2.04',
                true,
                "Versao",
                true
            );
        
            $dom->appendChild($root);
            $xmlCabec = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());   
      
            $arguments = [$function => [
                'nfseCabecMsg' => $xmlCabec,
                'nfseDadosMsg' => $xmlAssinado,
                ]
            ];

            $options = [];
            $client->__soapCall($function, $arguments, $options);

            //Log::info($client->__getLastRequest());

            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $client->__getLastResponse());
            //Log::info("Resultado Envio WebService");
            //Log::info($response);
            $xml = simplexml_load_string( $response );
            return $xml->sBody;
        } catch (\Exception $e) {
            echo $e->getFile();
            echo " - Erro Interno Envia Requisição - Linha: " . $e->getLine();
            echo "<pre>";
            print_r( $e->getMessage() );
        }
    }

    protected function sendRequest($xmlAssinado, $function, $empresa){
        $endPointUrl = EndPoint::where('codigo_municipio', $empresa->cidade_id)
            ->first();
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresa->id)
            ->first();

        $wsdl       = $endPointUrl->url_endpoint2;
        $endPoint   = $endPointUrl->url_endpoint2;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificado = getenv("CAMINHO_CERTIFICADO_LOCAL").$empresa->cpf_cnpj."_certKEY.pem";
        }else{
            $certificado = getenv("CAMINHO_CERTIFICADO_PROD").$empresa->cpf_cnpj."_certKEY.pem";
        }
        $passwordCert = base64_decode($certificadoCliente->senha);
        
        $options = [
            'location' => $endPoint,
            'keep_alive' => false,//true
            'trace' => true,
            'local_cert' => $certificado,
            'passphrase' => $passwordCert,
            'cache_wsdl' => 0,
            'wsdl_cache' => WSDL_CACHE_NONE,
            'exceptions' => false,
            //'use' => SOAP_ENCODED,
            //'soap_version' => SOAP_1_2,
            'stream_context'=> stream_context_create([
                'http' => [
                    'protocol_version'=>'1.1',
                    'header' => 'Connection: Close',
                    'user_agent' => 'PHPSoapClient'
                ],
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]),
        ];

        try {
            $client = new \SoapClient($wsdl, $options);

            $dom = new Dom('1.0', 'utf-8');
            $root = $dom->createElement('cabecalho');    

            //versao - atributo do cabecalho
            $domAttribute = $dom->createAttribute('versao');
            $domAttribute->value = '1.00';
            $root->appendChild($domAttribute);

            //xmlns
            $domAttribute = $dom->createAttribute('xmlns');
            $domAttribute->value = 'http://www.abrasf.org.br/nfse.xsd';
            $root->appendChild($domAttribute);

            $dom->addChild(
                $root,
                'versaoDados',
                '2.04',
                true,
                "Versao",
                true
            );
        
            $dom->appendChild($root);
            $xmlCabec = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());   
      
            $arguments = [$function => [
                'nfseCabecMsg' => $xmlCabec,
                'nfseDadosMsg' => $xmlAssinado,
                ]
            ];

            $options = [];
            $client->__soapCall($function, $arguments, $options);
            
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $client->__getLastResponse());
            $xml = simplexml_load_string( $response );
            
            return $xml->sBody;
        } catch (\Exception $e) {
            echo $e->getFile();
            echo " - Erro Interno Envia Requisição Empresa - Linha: " . $e->getLine();
            echo "<pre>";
            print_r( $e->getMessage() );
        }
    }

    public function consultarDadosCadastrais($cnpj, $inscricaoMunicipal){
        $xml = '<ConsultarDadosCadastraisEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
<Pedido>
<Prestador>
<CpfCnpj>
<Cnpj>'.$cnpj.'</Cnpj>
</CpfCnpj>
<InscricaoMunicipal>'.$inscricaoMunicipal.'</InscricaoMunicipal>
</Prestador>
</Pedido>
</ConsultarDadosCadastraisEnvio>';
        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'ConsultarDadosCadastraisEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml);

        $resposta = $this->enviarRequisicao($xml, 'ConsultarDadosCadastrais');
        $array = json_decode(json_encode($resposta), TRUE);
        
        $mensagemConcatenada = '';
        
        if(!isset($array['ConsultarDadosCadastraisResponse']['ConsultarDadosCadastraisResposta']['Cadastro'])){
            $mensagensErro = $array['ConsultarDadosCadastraisResponse']['ConsultarDadosCadastraisResposta']['ListaMensagemRetorno']['MensagemRetorno'];

            foreach ($mensagensErro as $mensagem) {
                $mensagemConcatenada .= "Código: " . $mensagem['Codigo'] . ", ";
                $mensagemConcatenada .= "Mensagem: " . $mensagem['Mensagem'] . ", ";
                $mensagemConcatenada .= "Correção: " . $mensagem['Correcao'] . "; ";
            }

            // Remover o último "; " para evitar um ponto-e-vírgula extra
            $mensagemConcatenada = rtrim($mensagemConcatenada, '; ');

            return [
                'error' => true,
                'message' => $mensagemConcatenada,
            ];
        }

        dd($array);

        return $array['ConsultarDadosCadastraisResponse']['ConsultarDadosCadastraisResposta']['Cadastro'];
    }

    public function consultarServicoTomado($cnpj, $inscricaoMunicipal, $periodo)
    {
        $xml = '<ConsultarNfseServicoTomadoEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
            <Pedido>
                <Consulente>
                    <CpfCnpj>
                    '. $this->tipoEmpresaTag($cnpj) .'
                    </CpfCnpj>
                    <InscricaoMunicipal>'.$inscricaoMunicipal.'</InscricaoMunicipal>
                </Consulente>
                <PeriodoEmissao>
                    <DataInicial>'.$periodo[0].'</DataInicial>
                    <DataFinal>'.$periodo[1].'</DataFinal>
                </PeriodoEmissao>            
                <Prestador>
                    <CpfCnpj>
                        <Cnpj>02253249001378</Cnpj>
                    </CpfCnpj>
                    <InscricaoMunicipal>67597</InscricaoMunicipal>
                </Prestador>
                <Tomador>
                    <CpfCnpj>
                    '. $this->tipoEmpresaTag($cnpj) .'
                    </CpfCnpj>
                    <InscricaoMunicipal>'.$inscricaoMunicipal.'</InscricaoMunicipal>
                </Tomador>
                <Pagina>1</Pagina>
            </Pedido>
        </ConsultarNfseServicoTomadoEnvio>';

        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'ConsultarNfseServicoTomadoEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml);

        /*echo "<pre>";
        print_r($xml);
        exit;*/

        $resposta = $this->enviarRequisicao($xml, 'ConsultarNfseServicoTomado');
        $array = json_decode(json_encode($resposta), TRUE);

        dd($array);
    }

    public function consultarNotasEmitidas($cnpj, $inscricaoMunicipal, $periodo){
        $array = null;

        //versão 2.04
        $xml = '<ConsultarNfseServicoPrestadoEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
        <Pedido>
            <Prestador>
                <CpfCnpj>
                    '. $this->tipoEmpresaTag($cnpj) .'
                </CpfCnpj>
                <InscricaoMunicipal>'.$inscricaoMunicipal.'</InscricaoMunicipal>
            </Prestador>
           <PeriodoEmissao>
                <DataInicial>'.$periodo[0].'</DataInicial>
                <DataFinal>'.$periodo[1].'</DataFinal>
           </PeriodoEmissao>
           <Pagina>1</Pagina>
        </Pedido>
     </ConsultarNfseServicoPrestadoEnvio>';
        
        $xml = $this->assinarRpsRepetidamente($xml, 'ConsultarNfseServicoPrestadoEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);

        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml);   

        $retorno = $this->enviarRequisicao($xml, 'ConsultarNfseServicoPrestado');
        $dados = $retorno->ConsultarNfseServicoPrestadoResponse->ConsultarNfseServicoPrestadoResposta;
        
        if(!isset($dados->ListaMensagemRetorno))
        {
           $array = $dados;
        }
        return $array;
    }

    /*
    processo de assinar documento xml com dados da empresa logada
    */
    public function assinarXml($xml, $tag = ''){
        $oCert = new Certificado();        

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        if(request()->session()->has('empresa_selecionada')){
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = Empresa::find($empresaSessao);
        }else{
            $empresaSessao = request()->session()->get('dados_empresa');
        }
        
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();
        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->signXML($xml, $tag);
        return $s;
    }

    public function assinarXmlTag($xml, $tag = ''){
        $oCert = new Certificado();        

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        if(request()->session()->has('empresa_selecionada')){
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = Empresa::find($empresaSessao);
        }else{
            $empresaSessao = request()->session()->get('dados_empresa');
        }
        
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();
        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        //assinatura para substituição
        $s = $oCert->signXMLMod3($xml, $tag);
        return $s;
    }

    /*
    Processo para Assinatura de XML pegando dados via API    
    */
    public function assinarXmlApi($xml, $tag = '', $prestadorCpfCnpj){
        $oCert = new Certificado();        

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        $empresaSessao = Empresa::where('cpf_cnpj', $prestadorCpfCnpj)
            ->first();
        
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();

        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->signXML($xml, $tag);
        return $s;
    }

    public function calculoDatas($grupoDatas){
        $inicio = end($grupoDatas);
        $limiteDatas = Carbon::now()->format("Y-m-d");
        $dataInicial = Carbon::parse($inicio[1])
            ->addDay(1)
            ->format('Y-m-d');
        $dataFinal = Carbon::parse($dataInicial)->addDays(9)->format('Y-m-d');

        if(date('Y-m-d', strtotime($dataFinal)) >= date('Y-m-d', strtotime($limiteDatas))){
            $grupoDatas[] = [$dataInicial,$limiteDatas];
        }else{
            $grupoDatas[] = [$dataInicial,$dataFinal];
            return $this->calculoDatas($grupoDatas);
        }

        return $grupoDatas;
    }

    protected function consultarXmlNota($nota){
        $empresaSessao = Session::get('empresa_selecionada');
        $empresaSessao = Empresa::find($empresaSessao);

        //abrasf 2.04
        //CONSULTAR NFSE SERVIÇO PRESTADO - NÃO RETORNA O XML
        $xml='
            <ConsultarNfseServicoPrestadoEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
                <Pedido>
                    <Prestador>
                        <CpfCnpj>
                            '. $this->tipoEmpresaTag($empresaSessao->cpf_cnpj) .'
                        </CpfCnpj>
                        <InscricaoMunicipal>'.$empresaSessao->inscricao_municipal.'</InscricaoMunicipal>
                    </Prestador>
                    <NumeroNfse>'.$nota->num_nfse.'</NumeroNfse>
                    <Pagina>1</Pagina>
                </Pedido>
            </ConsultarNfseServicoPrestadoEnvio>
        ';

        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'ConsultarNfseServicoPrestadoEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));
        $xml = ltrim($xml);

        $retorno = $this->enviarRequisicao($xml, 'ConsultarNfseServicoPrestado');
        $xmlCompleto = $retorno->ConsultarNfseServicoPrestadoResponse->ConsultarNfseServicoPrestadoResposta->asXML();
        
        $nomeArquivo = $empresaSessao->inscricao_municipal . '_NotaFiscaldeServicoEletronicaNFSe_' . str_pad($nota->num_nfse, 6, '0', STR_PAD_LEFT) . '.xml';
        Storage::disk('local')->put('public/' . $empresaSessao->id . '/' . $nomeArquivo, $xmlCompleto);
        $caminhoDownload = storage_path() . '/app/public/' . $empresaSessao->id . '/' . $nomeArquivo;
        header('Content-disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-type: "text/xml"; charset="utf8"');
        readfile($caminhoDownload);
    }

    protected function cancelarNfse($dados){
        $empresaSessao = request()->session()->get('empresa_selecionada');
        $empresaSessao = Empresa::find($empresaSessao);
        $id = 's'.time();

        $xml = '
        <CancelarNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
            <Pedido>
                <InfPedidoCancelamento Id="'. $id .'">
                    <IdentificacaoNfse>
                        <Numero>'.$dados['num_nfse'].'</Numero>
                        <CpfCnpj>
                            '. $this->tipoEmpresaTag($empresaSessao->cpf_cnpj) .'
                        </CpfCnpj>
                        <InscricaoMunicipal>'.$empresaSessao->inscricao_municipal.'</InscricaoMunicipal>
                        <CodigoMunicipio>'.$empresaSessao->cidade_id.'</CodigoMunicipio>
                    </IdentificacaoNfse>
                    <CodigoCancelamento>'.$dados['motivo'].'</CodigoCancelamento>
                </InfPedidoCancelamento>
            </Pedido>
        </CancelarNfseEnvio>
        ';

        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'Pedido'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));
        $xml = ltrim($xml);

        $retorno = $this->enviarRequisicao($xml, 'CancelarNfse');

        return $retorno->CancelarNfseResponse->CancelarNfseResposta;
    }


    protected function cancelarNfseApi($dados, $empresaSessao){
        $id = 's'.time();

        $xml = '
        <CancelarNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">
            <Pedido>
                <InfPedidoCancelamento Id="'. $id .'">
                    <IdentificacaoNfse>
                        <Numero>'.$dados['num_nfse'].'</Numero>
                        <CpfCnpj>
                            '. $this->tipoEmpresaTag($empresaSessao->cpf_cnpj) .'
                        </CpfCnpj>
                        <InscricaoMunicipal>'.$empresaSessao->inscricao_municipal.'</InscricaoMunicipal>
                        <CodigoMunicipio>'.$empresaSessao->cidade_id.'</CodigoMunicipio>
                    </IdentificacaoNfse>
                    <CodigoCancelamento>'.$dados['motivo_cancelamento'].'</CodigoCancelamento>
                </InfPedidoCancelamento>
            </Pedido>
        </CancelarNfseEnvio>
        ';

        $xmlNotaAssinado = $this->assinarRpsRepetidamenteApi($xml, 'Pedido', $empresaSessao->cpf_cnpj); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));
        $xml = ltrim($xml);

        $retorno = $this->sendRequest($xml, 'CancelarNfse', $empresaSessao);

        return $retorno->CancelarNfseResponse->CancelarNfseResposta;
    }

    /* GERAÇÃO XML - SUBSTITUIÇÃO */
    protected function gerarXmlSubstituicao204($dados,$rpss, $lote = 1){
        //$qtdRps = count($rpss);
        //$lote = time() + rand();

        $remetenteTipoDoc = $rpss[0]->infPrestador['tipo'];
        $remetenteCNPJCPF = $rpss[0]->infPrestador['cnpjcpf'];
        $inscricaoMunicipal = $rpss[0]->infPrestador['im'];
        $codigoMunicipio = $dados['cidade_id']; // Ex: código IBGE do município
        $codigoCancelamento = $dados['motivo'];
        
        // Cria documento
        $dom = new \DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = true;

        // Elemento raiz
        $root = $dom->createElement("SubstituirNfseEnvio");
        $root->setAttribute("xmlns", "http://www.abrasf.org.br/nfse.xsd");
        $dom->appendChild($root);

        // SubstituicaoNfse
        $substituicao = $dom->createElement("SubstituicaoNfse");
        $root->appendChild($substituicao);

        // Pedido
        $pedido = $dom->createElement("Pedido");
        $substituicao->appendChild($pedido);

        // InfPedidoCancelamento
        $infPedido = $dom->createElement("InfPedidoCancelamento");
        $infPedido->setAttribute("Id", "s01");
        $pedido->appendChild($infPedido);

        // IdentificacaoNfse
        $identificacao = $dom->createElement("IdentificacaoNfse");
        $infPedido->appendChild($identificacao);

        // Numero
        $identificacao->appendChild($dom->createElement("Numero", $dados['num_nfse']));

        // CpfCnpj
        $cpfCnpj = $dom->createElement("CpfCnpj");
        if ($remetenteTipoDoc == '2') {
            $cpfCnpj->appendChild($dom->createElement("Cnpj", $remetenteCNPJCPF));
        } else {
            $cpfCnpj->appendChild($dom->createElement("Cpf", $remetenteCNPJCPF));
        }

        $identificacao->appendChild($cpfCnpj);

        // InscricaoMunicipal
        $identificacao->appendChild($dom->createElement("InscricaoMunicipal", $inscricaoMunicipal));

        // CodigoMunicipio
        $identificacao->appendChild($dom->createElement("CodigoMunicipio", $codigoMunicipio));

        // CodigoCancelamento
        $infPedido->appendChild($dom->createElement("CodigoCancelamento", $codigoCancelamento));


        //adicionando o rps
        $domxmlRps = new \DOMDocument('1.0');
        $domxmlRps->preserveWhiteSpace = false;
        $domxmlRps->formatOutput = true;
        $domxmlRps->loadXML( $this->assinarXml(RenderRPS::toXml($rpss[0]), 'Rps') );
        $rpsXmlString = str_replace('<?xml version="1.0"?>', ' ', trim($domxmlRps->saveXML()));   
        $rpsXmlString = ltrim($rpsXmlString);

        // Importa o nó raiz do RPS para o documento principal
        $rpsNode = $dom->importNode($domxmlRps->documentElement, true);
        // Adiciona após </Pedido>
        $substituicao->appendChild($rpsNode);
        //final adição rps

        $content = $dom->saveXML();

        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $content =  $domxml->loadXML($this->assinarXmlTag($content, 'InfPedidoCancelamento'));
        $content = $domxml->saveXML();

        $content = str_replace('<Rps xmlns:default="http://www.w3.org/2000/09/xmldsig#">', '<Rps>', $content);
        $content = str_replace('default:', '', $content);
        
        return $content;
    }

    /*
    GERANDO O XML NO PADRÃO
    */
    protected function gerarXmlAbrasf204($rpss, $lote = 1){
        $qtdRps = count($rpss);
        
        $lote = time() + rand();

        $remetenteTipoDoc = $rpss[0]->infPrestador['tipo'];
        $remetenteCNPJCPF = $rpss[0]->infPrestador['cnpjcpf'];
        $inscricaoMunicipal = $rpss[0]->infPrestador['im'];

        $method = 'EnviarLoteRpsSincronoEnvio';
        $xsd = 'nfse';

        $content = "<$method xmlns=\"http://www.abrasf.org.br/$xsd.xsd\">";

        $content .= "<LoteRps Id=\"$lote\" versao=\"2.04\" >";
        $content .= "<NumeroLote>$lote</NumeroLote>";

        $content .= "<Prestador>";
        $content .= "<CpfCnpj>";
        if ($remetenteTipoDoc == '2') {
            $content .= "<Cnpj>$remetenteCNPJCPF</Cnpj>";
        } else {
            $content .= "<Cpf>$remetenteCNPJCPF</Cpf>";
        }
        $content .= "</CpfCnpj>";
        $content .= "<InscricaoMunicipal>$inscricaoMunicipal</InscricaoMunicipal>";
        $content .= "</Prestador>";
        $content .= "<QuantidadeRps>$qtdRps</QuantidadeRps>";

        $content .= "<ListaRps>";
        foreach ($rpss as $rps) {
            $domxml = new \DOMDocument('1.0');
            $domxml->preserveWhiteSpace = false;
            $domxml->formatOutput = true;
            $domxml->loadXML( $this->assinarXml(RenderRPS::toXml($rps), 'Rps') );
            $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
            $xml = ltrim($xml);   
            $content .= $xml;
        }
        $content .= "</ListaRps>";
        $content .= "</LoteRps>";
        $content .= "</$method>";
       
        return $content;
    }

    /*
    Método para gerar o XML no Padrão Abrasf 2.04 - API
    Criado em: 08/08/2023
    */
    protected function gerarXmlAbrasf204Api($rpss, $lote = 1){
        $qtdRps = count($rpss);
        
        $lote = time() + rand();

        $remetenteTipoDoc = $rpss[0]->infPrestador['tipo'];
        $remetenteCNPJCPF = $rpss[0]->infPrestador['cnpjcpf'];
        $inscricaoMunicipal = $rpss[0]->infPrestador['im'];

        $method = 'EnviarLoteRpsSincronoEnvio';
        $xsd = 'nfse';

        $content = "<$method xmlns=\"http://www.abrasf.org.br/$xsd.xsd\">";

        $content .= "<LoteRps Id=\"$lote\" versao=\"2.04\" >";
        $content .= "<NumeroLote>$lote</NumeroLote>";

        $content .= "<Prestador>";
        $content .= "<CpfCnpj>";
        if ($remetenteTipoDoc == '2') {
            $content .= "<Cnpj>$remetenteCNPJCPF</Cnpj>";
        } else {
            $content .= "<Cpf>$remetenteCNPJCPF</Cpf>";
        }
        $content .= "</CpfCnpj>";
        $content .= "<InscricaoMunicipal>$inscricaoMunicipal</InscricaoMunicipal>";
        $content .= "</Prestador>";
        $content .= "<QuantidadeRps>$qtdRps</QuantidadeRps>";

        $content .= "<ListaRps>";
        foreach ($rpss as $rps) {
            $domxml = new \DOMDocument('1.0');
            $domxml->preserveWhiteSpace = false;
            $domxml->formatOutput = true;
            $domxml->loadXML( $this->assinarXmlApi(RenderRPS::toXml($rps), 'Rps', $remetenteCNPJCPF) );
            $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
            $xml = ltrim($xml);   
            $content .= $xml;
        }
        $content .= "</ListaRps>";
        $content .= "</LoteRps>";
        $content .= "</$method>";
       
        return $content;
    }

    public function assinarRpsRepetidamente($xml, $tag = '')
    {
        $oCert = new Certificado();        

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        if(request()->session()->has('empresa_selecionada')){
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = Empresa::find($empresaSessao);
        }else{
            $empresaSessao = request()->session()->get('dados_empresa');
        }
        
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();

        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->signXMLMod2($xml, $tag);
        return $s;
    }

    public function assinarRpsRepetidamenteApi($xml, $tag = '', $prestadorCpfCnpj)
    {
        $oCert = new Certificado();        

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        $empresaSessao = Empresa::where('cpf_cnpj', $prestadorCpfCnpj)->first();
       
        $certificadoCliente = CertificadoEmpresa::where('empresa_id', $empresaSessao->id)
            ->first();
        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->signXMLMod2($xml, $tag);
        return $s;
    }
}