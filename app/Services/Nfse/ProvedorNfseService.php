<?php

namespace App\Services\Nfse;

use App\Models\Empresa;
use App\Services\FocuNfe\GestaoService;
use App\Services\Spedy\SpedyService;
use App\Utilitarios\Utilitarios;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use RuntimeException;
use Throwable;

class ProvedorNfseService
{
    public function __construct(
        private SpedyService $spedyService,
        private GestaoService $focuNfeService,
        private CertificateManager $certManager,
    ) {
    }

    /**
     * Sincroniza a empresa com o provedor de NFS-e informado.
     */
    public function salvar(Empresa $empresa, string $provedor): void
    {
        match (strtolower($provedor)) {
            //'spedy' => $this->salvarSpedy($empresa),
            'focunfe', 'focusnfe' => $this->salvarFocusNfe($empresa),
            default => throw new RuntimeException(
                "Provedor de NFS-e não suportado: {$provedor}"
            ),
        };
    }

    /*private function salvarSpedy(Empresa $empresa): void
    {
        $cidade = $empresa->cidade;
        $estado = $cidade?->estado;

        if (!$cidade || !$estado) {
            throw new RuntimeException(
                'A empresa precisa possuir uma cidade e um estado cadastrados.'
            );
        }

        $data = [
            'name' => $empresa->razao_social,
            'legalName' => $empresa->nome_fantasia,
            'federalTaxNumber' => $empresa->cpf_cnpj,
            'address' => [
                'street' => $empresa->logradouro,
                'district' => $empresa->bairro,
                'postalCode' => Utilitarios::somenteNumeros(
                    $empresa->cep
                ),
                'number' => $empresa->numero,
                'additionalInformation' => $empresa->complemento,
                'city' => [
                    'code' => $empresa->cidade_id,
                    'name' => $cidade->municipio,
                    'state' => strtolower($estado->sigla),
                ],
                'country' => [
                    'code' => '1058',
                    'name' => 'Brasil',
                ],
                'cityName' => $cidade->municipio,
            ],
            'stateTaxNumber' => null,
            'cityTaxNumber' => $empresa->inscricao_municipal,
            'email' => $empresa->email,
            'phone' => Utilitarios::somenteNumeros(
                $empresa->telefone1
            ),
            'mobilePhone' => Utilitarios::somenteNumeros(
                $empresa->telefone2
            ),
            'taxRegime' => $empresa->regime,
            'specialTaxRegime' => $empresa->regime_especial_tributacao,
            'simplesNacionalTaxRegime' => $empresa->simples_nacional_regime,
            'economicActivities' => [],
        ];

        foreach ($empresa->atividadesEmpresa as $key => $atividade) {
            $data['economicActivities'][] = [
                'code' => $atividade->codigo_atividade,
                'type' => $key === 0 ? 'main' : 'secondary',
            ];
        }

        if (empty($empresa->spedy_id)) {
            $response = $this->spedyService->createCompany($data);

            if (empty($response['id'])) {
                throw new RuntimeException(
                    'A Spedy não retornou o identificador da empresa.'
                );
            }

            $empresa->spedy_id = $response['id'];
            $empresa->spedy_api_key =
                $response['apiCredentials']['apiKey'] ?? null;

            $empresa->save();
        } else {
            $this->spedyService->updateCompany(
                $empresa->spedy_id,
                $data
            );
        }

        $this->sincronizarCertificadoSpedy($empresa);

        $this->spedyService->setConfigurations(
            $empresa->spedy_id,
            [
                'serviceInvoice' => [
                    'series' => $empresa->serie_dps,
                    'issueType' => 'website',
                    'environmentType' =>
                        $empresa->ambiente_emissao === 'HOMOLOGACAO'
                            ? 'development'
                            : 'production',
                    'nextNumber' => $empresa->num_ultimo_dps,
                ],
            ]
        );
    }*/

   /*private function sincronizarCertificadoSpedy(Empresa $empresa): void
    {
        $certificado = $empresa->certificado->first();

        if (!$certificado) {
            return;
        }

        $certificados = $this->spedyService->verificarCertificado(
            $empresa->spedy_id
        );

        if (isset($certificados[0]['id'])) {
            return;
        }

        $cd = $this->certManager->getCertificate($empresa->id);

        $this->spedyService->uploadCertificate(
            $empresa->spedy_id,
            $cd['pfx'],
            $cd['password']
        );
    }*/

    private function salvarFocusNfe(Empresa $empresa): void
    {
        $cidade = $empresa->cidade;
        $estado = $cidade?->estado;

        if (!$cidade || !$estado) {
            throw new RuntimeException(
                'A empresa precisa possuir uma cidade e um estado cadastrados.'
            );
        }

        $regimeTributario = match ($empresa->regime) {
            'simplesNacional' => 1,
            'simplesNacionalExcessoSublimite' => 2,
            'regimeNormal' => 3,
            'simplesNacionalMEI' => 4,
            default => 1,
        };

        $dados = [
            'nome' => $empresa->razao_social,
            'nome_fantasia' => $empresa->nome_fantasia,
            'cnpj' => $empresa->cpf_cnpj,
            'inscricao_municipal' => $empresa->inscricao_municipal,
            'cep' => $empresa->cep,
            'bairro' => $empresa->bairro,
            'complemento' => $empresa->complemento,
            'logradouro' => $empresa->logradouro,
            'municipio' => $cidade->municipio,
            'numero' => $empresa->numero,
            'pais' => 'Brasil',
            'regime_tributario' => $regimeTributario,
            'telefone' => '',
            'uf' => $estado->sigla,
            'habilita_nfse' => true,

            'proximo_numero_nfse_producao' => null,
            'serie_nfse_producao' => null,

            'proximo_numero_nfse_homologacao' => '7',
            'serie_nfse_homologacao' => '0008',
        ];

        $certificado = $empresa->certificado->where('empresa_id', $empresa->id)->first();
        
        if ($certificado) {
            $cd = $this->certManager->getCertificate($empresa->id);

            $conteudo = file_get_contents($cd['pfx']);

            if ($conteudo === false) {
                throw new RuntimeException(
                    'Não foi possível ler o certificado digital.'
                );
            }

            $dados['arquivo_certificado_base64'] = base64_encode($conteudo);
            $dados['senha_certificado'] = $cd['password'];
        }

        dd($dados, $certificado);

        if (!empty($empresa->focunfe_id)) {
            $resultado = $this->focuNfeService->atualizarEmpresa(
                $empresa->focunfe_id,
                $dados
            );
        } else {
            $resultado = $this->focuNfeService->criarEmpresa($dados);
        }

        if (empty($resultado['id'])) {
            throw new RuntimeException(
                'O provedor Focus NFe não retornou o identificador da empresa.'
            );
        }

        $empresa->focunfe_id = $resultado['id'];
        $empresa->focunfe_token_hmg =
            $resultado['token_homologacao'] ?? $empresa->focunfe_token_hmg;
        $empresa->focunfe_token_prd =
            $resultado['token_producao'] ?? $empresa->focunfe_token_prd;

        $empresa->save();
    }
}
