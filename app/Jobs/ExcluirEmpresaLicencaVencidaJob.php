<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcluirEmpresaLicencaVencidaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public int $empresaId,
        public int $dias = 90
    ) {}

    public function handle(): void
    {
        $empresaId = $this->empresaId;

        $exclusoes = DB::transaction(function () use ($empresaId) {

            // Bloqueia a empresa durante a operação
            $empresa = DB::table('empresas')
                ->where('id', $empresaId)
                ->lockForUpdate()
                ->first();

            if (!$empresa) {
                Log::warning('Empresa não encontrada para exclusão.', [
                    'empresa_id' => $empresaId,
                ]);

                return null;
            }

            // Verifica novamente a validade da última licença
            $dataLimite = Carbon::now()
                ->subDays($this->dias)
                ->toDateString();

            $ultimaLicenca = DB::table('licenses')
                ->where('empresa_id', $empresaId)
                ->max('validate');

            if (!$ultimaLicenca || $ultimaLicenca >= $dataLimite) {
                Log::info('Exclusão cancelada: empresa possui licença dentro do prazo.', [
                    'empresa_id' => $empresaId,
                    'ultima_licenca' => $ultimaLicenca,
                ]);

                return null;
            }

            $database = DB::getDatabaseName();

            // Busca tabelas que possuem empresa_id
            $tabelas = DB::table('information_schema.columns')
                ->where('TABLE_SCHEMA', $database)
                ->where('COLUMN_NAME', 'empresa_id')
                ->whereNotNull('TABLE_NAME')
                ->distinct()
                ->pluck('TABLE_NAME')
                ->toArray();

            // Remove a tabela principal da lista
            $tabelas = array_values(array_diff($tabelas, ['empresas']));

            $conjunto = array_flip($tabelas);

            // Busca relacionamentos entre tabelas
            $relacionamentos = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->select([
                    'TABLE_NAME as tabela_filha',
                    'REFERENCED_TABLE_NAME as tabela_pai',
                ])
                ->where('TABLE_SCHEMA', $database)
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->get();

            // Grafo: tabela filha -> tabela pai
            $grafo = [];
            $grauEntrada = [];

            foreach ($tabelas as $tabela) {
                $grafo[$tabela] = [];
                $grauEntrada[$tabela] = 0;
            }

            foreach ($relacionamentos as $relacao) {

                $filha = $relacao->tabela_filha;
                $pai = $relacao->tabela_pai;

                if (
                    isset($conjunto[$filha]) &&
                    isset($conjunto[$pai]) &&
                    !in_array($pai, $grafo[$filha], true)
                ) {
                    $grafo[$filha][] = $pai;
                    $grauEntrada[$pai]++;
                }
            }

            // Ordenação topológica: filhos antes dos pais
            $fila = [];

            foreach ($grauEntrada as $tabela => $grau) {
                if ($grau === 0) {
                    $fila[] = $tabela;
                }
            }

            $ordem = [];

            while (!empty($fila)) {

                $tabela = array_shift($fila);

                $ordem[] = $tabela;

                foreach ($grafo[$tabela] as $pai) {

                    $grauEntrada[$pai]--;

                    if ($grauEntrada[$pai] === 0) {
                        $fila[] = $pai;
                    }
                }
            }

            // Verifica dependências cíclicas
            if (count($ordem) !== count($tabelas)) {
                throw new \RuntimeException(
                    'Não foi possível determinar a hierarquia das tabelas. Existem relacionamentos cíclicos.'
                );
            }

            $resultado = [];

            // Exclui os registros filhos primeiro
            foreach ($ordem as $tabela) {

                $quantidade = DB::table($tabela)
                    ->where('empresa_id', $empresaId)
                    ->delete();

                $resultado[$tabela] = $quantidade;
            }

            // Exclui a empresa por último
            $resultado['empresas'] = DB::table('empresas')
                ->where('id', $empresaId)
                ->delete();

            return $resultado;
        });

        if ($exclusoes === null) {
            return;
        }

        Log::info('Empresa excluída por licença vencida.', [
            'empresa_id' => $empresaId,
            'dias_vencidos' => $this->dias,
            'sucesso' => true,
            'exclusoes' => $exclusoes,
            'total_registros' => array_sum($exclusoes),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao excluir empresa com licença vencida.', [
            'empresa_id' => $this->empresaId,
            'erro' => $exception->getMessage(),
        ]);
    }
}
