<?php

namespace App\Services\Cnaes;

use App\Models\CorrelacaoTribMunTribNac;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Integração com a API de CNAEs (cnaes.portalnotafacil.com.br), que retorna
 * os itens da LC 116 relacionados a cada CNAE.
 */
class CnaesApiService
{
    private const TOKEN_CACHE_KEY = 'api_cnaes_token';

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.cnaes.url'), '/');
    }

    /**
     * Consulta os itens da LC 116 vinculados a um CNAE.
     *
     * @return array Lista de itens no formato da API (codigo, codigo_numerico, descricao...)
     * @throws Exception
     */
    public function consultarItensLc(string $cnae): array
    {
        $cnae = preg_replace('/\D/', '', $cnae);

        $response = $this->get('/consulta', ['cnae' => $cnae]);

        // Token expirado/revogado: renova e tenta novamente uma vez
        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $response = $this->get('/consulta', ['cnae' => $cnae]);
        }

        if ($response->status() === 404) {
            return [];
        }

        if ($response->failed()) {
            throw new Exception("Falha ao consultar API de CNAEs para o CNAE {$cnae} (HTTP {$response->status()}).");
        }

        return $response->json('itens') ?? [];
    }

    /**
     * Busca os itens da LC 116 de todos os CNAEs da empresa e grava em empresa_atividades.
     *
     * @return int Quantidade de atividades gravadas/atualizadas
     * @throws Exception
     */
    public function sincronizarAtividades(Empresa $empresa): int
    {
        $cnaes = EmpresaCnae::where('empresa_id', $empresa->id)
            ->pluck('codigo_cnae')
            ->unique();

        $itens = [];
        foreach ($cnaes as $cnae) {
            foreach ($this->consultarItensLc($cnae) as $item) {
                $codigo = $this->normalizarCodigo($item['codigo_numerico'] ?? $item['codigo'] ?? '');
                if ($codigo === '') {
                    continue;
                }
                // Um mesmo item LC pode vir de mais de um CNAE
                $itens[$codigo] = $item['codigo'] . ' ' .$item['descricao'] ?? '';
            }
        }

        if (empty($itens)) {
            return 0;
        }

        DB::transaction(function () use ($empresa, $itens) {
            foreach ($itens as $codigo => $descricao) {
                $atividade = EmpresaAtividade::firstOrNew([
                    'empresa_id' => $empresa->id,
                    'codigo_atividade' => $codigo,
                ]);

                $atividade->descricao_atividade = $descricao;

                $aliquota = $this->buscarAliquota($empresa->id, $codigo);
                if ($aliquota !== null) {
                    $atividade->aliquota = $aliquota;
                }

                $atividade->save();
            }
        });

        return count($itens);
    }

    /**
     * "0716" / "07.16" -> "716"
     */
    private function normalizarCodigo(string $codigo): string
    {
        return ltrim(preg_replace('/\D/', '', $codigo), '0');
    }

    /**
     * Alíquota da tabela correlacao_trib_mun_trib_nac pelo cTribMun,
     * priorizando a correlação da própria empresa.
     */
    private function buscarAliquota(int $empresaId, string $codigo): ?string
    {
        return CorrelacaoTribMunTribNac::where('cTribMun', $codigo)
            ->whereNotNull('aliquota')
            ->orderByRaw('empresa_id = ? DESC', [$empresaId])
            ->orderByDesc('id')
            ->value('aliquota');
    }

    private function get(string $path, array $query)
    {
        return Http::acceptJson()
            ->withToken($this->token())
            ->timeout(15)
            ->get($this->baseUrl . $path, $query);
    }

    /**
     * Autentica na API (email + chave) e mantém o token em cache.
     *
     * @throws Exception
     */
    private function token(): string
    {
        return Cache::remember(self::TOKEN_CACHE_KEY, now()->addHours(12), function () {
            $email = config('services.cnaes.email');
            $chave = config('services.cnaes.chave');

            if (empty($email) || empty($chave)) {
                throw new Exception('API_CNAES_EMAIL / API_CNAE_CHAVE não configurados no .env.');
            }

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->baseUrl . '/auth/login', [
                    'email' => $email,
                    'password' => $chave,
                ]);

            $token = $response->json('token');

            if ($response->failed() || empty($token)) {
                throw new Exception('Falha ao autenticar na API de CNAEs (HTTP ' . $response->status() . '): ' . $response->json('message'));
            }

            return $token;
        });
    }
}
