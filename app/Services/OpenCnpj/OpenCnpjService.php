<?php

namespace App\Services\OpenCnpj;

use App\Models\Empresa;
use App\Models\EmpresaCnae;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OpenCnpjService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.opencnpj.url', 'https://api.opencnpj.org'), '/');
    }

    /**
     * Consulta os dados da Receita Federal de um CNPJ na OpenCNPJ.
     *
     * @throws Exception
     */
    public function consultar(string $cnpj): array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            throw new Exception('CNPJ inválido para consulta na OpenCNPJ: ' . $cnpj);
        }

        $response = Http::acceptJson()
            ->timeout(15)
            ->retry(2, 500, null, false)
            ->get("{$this->baseUrl}/{$cnpj}", ['datasets' => 'receita']);

        if ($response->status() === 404) {
            throw new Exception('CNPJ não encontrado na OpenCNPJ: ' . $cnpj);
        }

        if ($response->failed()) {
            throw new Exception('Falha ao consultar OpenCNPJ (HTTP ' . $response->status() . ').');
        }

        return $response->json() ?? [];
    }

    /**
     * Retorna os CNAEs do CNPJ no formato [codigo, descricao, is_principal].
     */
    public function consultarCnaes(string $cnpj): array
    {
        $dados = $this->consultar($cnpj);

        return collect($dados['cnaes'] ?? [])
            ->filter(fn ($cnae) => !empty($cnae['codigo']))
            ->map(fn ($cnae) => [
                'codigo' => preg_replace('/\D/', '', $cnae['codigo']),
                'descricao' => $cnae['descricao'] ?? '',
                'is_principal' => (bool) ($cnae['is_principal'] ?? false),
            ])
            ->values()
            ->all();
    }

    /**
     * Consulta os CNAEs do CNPJ da empresa e os grava em empresa_cnaes,
     * substituindo os existentes. Define o CNAE principal em empresas.empresa_cnae_id.
     *
     * @return int Quantidade de CNAEs gravados
     * @throws Exception
     */
    public function sincronizarCnaes(Empresa $empresa): int
    {
        // CPF não tem CNAE
        if (strlen(preg_replace('/\D/', '', $empresa->cpf_cnpj)) !== 14) {
            return 0;
        }

        $cnaes = $this->consultarCnaes($empresa->cpf_cnpj);

        if (empty($cnaes)) {
            return 0;
        }

        DB::transaction(function () use ($empresa, $cnaes) {
            //EmpresaCnae::where('empresa_id', $empresa->id)->delete();

            $principalId = null;
            foreach ($cnaes as $cnae) {
                /*$registro = EmpresaCnae::create([
                    'empresa_id' => $empresa->id,
                    'codigo_cnae' => $cnae['codigo'],
                    'descricao_cnae' => $cnae['descricao'],
                    'principal' => $cnae['is_principal'] ? EmpresaCnae::PRINCIPAL : EmpresaCnae::SECUNDARIO,
                ]);*/
                $registro = EmpresaCnae::updateOrCreate(
                    [
                        'empresa_id' => $empresa->id,
                        'codigo_cnae' => $cnae['codigo'],
                    ],
                    [
                        'descricao_cnae' => $cnae['descricao'],
                        'principal' => $cnae['is_principal']
                            ? EmpresaCnae::PRINCIPAL
                            : EmpresaCnae::SECUNDARIO,
                    ]
                );

                if ($cnae['is_principal'] && $principalId === null) {
                    $principalId = $registro->id;
                }
            }

            if ($principalId !== null) {
                $empresa->empresa_cnae_id = $principalId;
                $empresa->save();
            }
        });

        return count($cnaes);
    }
}
