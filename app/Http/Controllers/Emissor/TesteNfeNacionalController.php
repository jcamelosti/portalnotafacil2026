<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaEmitida;
use App\Services\NotaFiscalServico\DanfsePdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use JCamelo\NfseNacionalLib\Services\NFSeService;

class TesteNfeNacionalController extends Controller
{
    private NFSeService $nfse;

    public function __construct(NFSeService $nfse, private CertificateManager $certManager)
    {
        $this->nfse = $nfse;
    }
    
    public function teste()
    {
        $empresa = Empresa::find(1); // Substitua pelo ID da empresa que deseja testar
        //string $provider, int $empresaId, string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final
        /*$response[] = $this->nfse->consultarUrlNfse(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            16, //nNFSe,
            '',//dt ini
            ''//dt fim
        );
        
        //Consulta do XML
        $response[] = $this->nfse->consultarXml(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            15, //nNFSe,
            '',//dt ini
            ''//dt fim
        );


        dd([
            'ConsultaNfseUrlTest' => $response[0],
            'ConsultarNfseXmlTest' => $response[1]
        ]);*/

        //Período Máximo de Consulta 30 dias.
        /*$retorno = $this->nfse->consultarNfseServicosPrestados(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal,
            null,
            '2026-08-30',//dt ini
            '2026-10-07',//dt fim
            null
        );*/

        /*$retorno = $this->nfse->consultarNfseServicosTomados(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal,
            null,
            '2026-07-01',//dt ini
            '2026-07-30',//dt fim
            null
        );*/

        //Utilitarios::sendMessage("Mensagem Para Vários Usuários do ChatBot");

        /*$nota = NotaEmitida::where('id', 61)->first();
        
        $danfsePdfService = app(DanfsePdfService::class);
        $pdf = $danfsePdfService->renderFromXml($nota->nfse_xml);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="NFSe.pdf"',
            'Content-Transfer-Encoding' => 'binary',
            'Accept-Ranges' => 'bytes',
        ]);*/

        $response = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<s:Body>
<ValidarXmlResponse xmlns="http://www.sped.fazenda.gov.br/nfse">
    <ValidarXmlResposta>
    <ListaMensagemRetorno>
        <MensagemRetorno>
        <Codigo>S000</Codigo>
        <Mensagem>XML válido. Validação realizada com sucesso.</Mensagem>
        </MensagemRetorno>
    </ListaMensagemRetorno>
    </ValidarXmlResposta>
</ValidarXmlResponse>
</s:Body>
</s:Envelope>';

        // Remove eventual BOM UTF-8
        $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);

        // Remove os prefixos dos namespaces, como s: e ds:
        $response = preg_replace(
            '/(<\/?)[a-zA-Z0-9_-]+:([^>]*>)/',
            '$1$2',
            $response
        );

        libxml_use_internal_errors(true);

        $retorno = simplexml_load_string(
            $response,
            \SimpleXMLElement::class,
            LIBXML_NONET
        );
        
        /*echo "<pre>";
        var_dump($retorno);

        echo "<br>";*/

        if ($retorno === false) {
            $erros = array_map(
                fn ($erro) => trim($erro->message),
                libxml_get_errors()
            );

            libxml_clear_errors();

            Log::error('Erro ao interpretar resposta SOAP', [
                'erros' => $erros,
                'response' => $response,
            ]);

            return null;
        }

        libxml_clear_errors();

        //no service 

        if ($retorno === null) {
            Log::error('Não foi possível interpretar o retorno da validação XML', [
                'empresa_id' => $empresa->id,
            ]);
        } else {
            $body = $retorno->Body ?? null;

            $resposta = $body?->ValidarXmlResponse ?? null;

            // Salva a resposta da validação quando existir
            if ($resposta !== null) {
                DB::insert(
                    'INSERT INTO validacoes_xml (empresa_id, xml) VALUES (?, ?)',
                    [
                        $empresa->id,
                        $resposta->asXML(),
                    ]
                );
            }

            $mensagens = $resposta
                ?->ValidarXmlResposta
                ?->ListaMensagemRetorno
                ?? null;

            if ($mensagens !== null) {
                $listaMensagens = $mensagens->MensagemRetorno ?? [];

                foreach ($listaMensagens as $mensagem) {
                    $codigo = (string) ($mensagem->Codigo ?? '');

                    // Registra somente mensagens de erro
                    if ($codigo !== '' && $codigo !== 'S000') {
                        DB::insert(
                            'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                            [
                                $empresa->id,
                                json_encode(
                                    $mensagem,
                                    JSON_UNESCAPED_UNICODE
                                ),
                            ]
                        );
                    }
                }
            }
        }
    }
}
