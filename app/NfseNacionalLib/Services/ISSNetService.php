<?php
namespace JCamelo\NfseNacionalLib\Services;

use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\DTO\CadastroDTO;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;

class ISSNetService
{
     public function __construct(
        private CertificateManager $certManager,
        private SoapTransport $transport
    ) {}
       
    public function gerarNfse(string $xml, int $empresaId)
    {
        $xml =  str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
        $soap = SoapBuilder::build('GerarNfse', $xml);
       
        $cert = $this->certManager->getCertificate($empresaId);
        $response = $this->transport->send(
            config('nfse.url'),
            config('nfse.uri'),
            'GerarNfse',
            $soap,
            $cert
        );
        echo "<br>";
        echo __METHOD__."<br><br>";
        echo "<br>";
        
        dd($response);
        return $this->parse($response);
    }

    public function cancelarNfse(string $xml)
    {
        //return $this->send('CancelarNfse', $xml);
    }

    public function consultarDadosCadastrais(string $xml, int $empresaId)
    {
        $soap = SoapBuilder::build('ConsultarDadosCadastrais', $xml);
        $cert = $this->certManager->getCertificate($empresaId);

        $response = $this->transport->send(
            config('nfse.url'),
            config('nfse.uri'),
            'ConsultarDadosCadastrais',
            $soap,
            $cert
        );
        $cadastroXml = $this->extractCadastro($response);
        dd($cadastroXml);
        return $this->toDTO($cadastroXml);
    }

    public function consultarUrlNfse(string $xml, int $empresaId)
    {
        $soap = SoapBuilder::build('ConsultarUrlNfse', $xml);
        $cert = $this->certManager->getCertificate($empresaId);

        $response = $this->transport->send(
            config('nfse.url'),
            config('nfse.uri'),
            'ConsultarUrlNfse',
            $soap,
            $cert
        );
        echo __METHOD__."<br><br>";
        dd($response);
        //return $this->parse($response);
    }

    public function extractCadastro(string $response): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = simplexml_load_string($response);

        if (!$xml) {
            //throw new \Exception("Erro XML");
            dd([
                'erros' => libxml_get_errors(),
                'response_inicio' => substr($response, 0, 500)
            ]);
        }

        // IGNORA namespaces
        $xml->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ns', 'http://www.sped.fazenda.gov.br/nfse');

        $nodes = $xml->xpath('//ns:Cadastro');

        if (!$nodes || !isset($nodes[0])) {
            throw new \Exception("Cadastro não encontrado");
        }

        return $nodes[0];
    }

    private function toDTO(\SimpleXMLElement $cadastro): CadastroDTO
    {
        //dd($cadastro);
        return new CadastroDTO(
            cnpj: (string) $cadastro->CNPJ,
            im: (string) $cadastro->IM,
            status: (string) $cadastro->StatusCadastro,
            razaoSocial: (string) $cadastro->xNome,
            fantasia: (string) $cadastro->xFant,

            endereco: [
                'logradouro' => (string) $cadastro->enderNac->xLgr,
                'numero' => (string) $cadastro->enderNac->nro,
                'bairro' => (string) $cadastro->enderNac->xBairro,
                'cidade' => (string) $cadastro->enderNac->cMun,
                'uf' => (string) $cadastro->enderNac->UF,
                'cep' => (string) $cadastro->enderNac->CEP,
            ],
            fone: (string) $cadastro->fone,
            email: (string) $cadastro->email,

            opcaoMei: (int) $cadastro->OpcaoMei->OptanteMei,
            optanteSimplesNacional: (int) $cadastro->OpcaoSimplesNacional->OptanteSimplesNacional,
            simplesNacional: $this->parseSimplesNacional($cadastro),
            atividades: $this->parseAtividades($cadastro),
            permiteOutrasDeducoes: (int)$cadastro->PermiteOutrasDeducoes,
            permiteDescontoCondicionado: (int)$cadastro->PermiteDescontoCondicionado,
            permiteDescontoIncondicionado: (int)$cadastro->PermiteDescontoIncondicionado,
            permiteExigibilidadeSuspensaDecisaoJudicial: (int)$cadastro->PermiteExigibilidadeSuspensaDecisaoJudicial,
            permiteExigibilidadeSuspensaProcAdm: (int)$cadastro->PermiteExigibilidadeSuspensaProcAdm,
            permiteTributarFora: (int)$cadastro->PermiteTributarFora,
            tributacoesPermitidas: [
                'tribISSQN' => (int)$cadastro->TributacoesPermitidas->tribISSQN
            ]
        );
    }

    private function parseSimplesNacional($cadastro): array
    {
        $result = [];

        if (isset($cadastro->OpcaoSimplesNacional->Vigencias->Vigencia)) {
            foreach ($cadastro->OpcaoSimplesNacional->Vigencias->Vigencia as $vig) {
                $result[] = [
                    'inicio' => (string) $vig->DataInicial,
                    'fim' => (string) ($vig->DataFinal ?? null),
                ];
            }
        }

        return $result;
    }

    private function parseAtividades($cadastro): array
    {
        $result = [];

        if (isset($cadastro->Atividades->Atividade)) {
            foreach ($cadastro->Atividades->Atividade as $atv) {
                $result[] = [
                    'codigo' => (string) $atv->cTribMun,
                    'descricao' => (string) $atv->xTribMun,
                    'aliquota' => (float) $atv->pAliq,
                ];
            }
        }

        return $result;
    }

    private function parse($response)
    {
        $xml = simplexml_load_string($response);
        $output = (string)$xml->xpath('//outputXML')[0];

        return simplexml_load_string($output);
    }

    public function recepcionarLoteDpsSincrono(string $xml, int $empresaId)
    {
        $xml =  str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
        $soap = SoapBuilder::build('RecepcionarLoteDpsSincrono', $xml);
       
        $cert = $this->certManager->getCertificate($empresaId);
        $response = $this->transport->send(
            config('nfse.url'),
            config('nfse.uri'),
            'RecepcionarLoteDpsSincrono',
            $soap,
            $cert
        );
        echo "<br>";
        echo __METHOD__."<br><br>";
        echo "<br>";
        
        dd($response);
        return $this->parse($response);
    }
}