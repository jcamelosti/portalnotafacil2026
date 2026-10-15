<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class ImportarCorrelacoesTribNacNbs extends Command
{
    protected $signature = 'importar:correlacoes-trib-nac-nbs
                            {--substituir : Apaga os registros existentes antes de importar}';

    protected $description = 'Importa o CSV de correlações de tributação nacional, NBS e IBS/CBS';

    public function handle(): int
    {
        $arquivo = storage_path(
            'app/importacoes/Correlacao_TribNac_NBS_cClassTribIBSCBS_CSTIBSCBS_IndOp.csv'
        );

        if (!is_file($arquivo)) {
            $this->error("Arquivo não encontrado: {$arquivo}");

            return self::FAILURE;
        }

        $handle = fopen($arquivo, 'r');

        if ($handle === false) {
            $this->error('Não foi possível abrir o arquivo CSV.');

            return self::FAILURE;
        }

        try {
            // O arquivo utiliza ponto e vírgula como separador.
            // A primeira linha contém os cabeçalhos.
            $cabecalho = fgetcsv($handle, 0, ';', '"', '');

            if ($cabecalho === false || count($cabecalho) !== 13) {
                throw new RuntimeException(
                    'Cabeçalho inválido: eram esperadas 13 colunas.'
                );
            }

            if ($this->option('substituir')) {
                if (!$this->confirm(
                    'Isso apagará todos os registros atuais da tabela. Deseja continuar?'
                )) {
                    fclose($handle);

                    return self::SUCCESS;
                }

                DB::table('correlacoes_trib_nac_nbs')->delete();
            }

            $lote = [];
            $total = 0;
            $linha = 1;
            $tamanhoLote = 500;

            while (($dados = fgetcsv($handle, 0, ';', '"', '')) !== false) {
                $linha++;

                // Ignora linhas vazias.
                if ($dados === [null] || $dados === []) {
                    continue;
                }

                if (count($dados) !== 13) {
                    throw new RuntimeException(
                        "Linha {$linha}: esperadas 13 colunas, encontradas "
                        . count($dados) . '.'
                    );
                }

                $lote[] = [
                    'c_trib_nac' => trim($dados[0]),
                    'x_trib_nac' => trim($dados[1]),
                    'c_nbs' => trim($dados[2]),
                    'x_nbs' => trim($dados[3]),
                    'c_class_trib' => trim($dados[4]),
                    'x_class_trib' => trim($dados[5]),
                    'cst' => trim($dados[6]),
                    'x_st' => trim($dados[7]),
                    'c_ind_op' => trim($dados[8]),
                    'x_ind_op' => trim($dados[9]),
                    'ps_onerosa' => trim($dados[10]) ?: null,
                    'adq_exterior' => trim($dados[11]) ?: null,
                    'local_incidencia_ibs' => trim($dados[12]) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($lote) >= $tamanhoLote) {
                    DB::transaction(function () use (&$lote) {
                        DB::table('correlacoes_trib_nac_nbs')
                            ->insert($lote);
                    });

                    $total += count($lote);
                    $lote = [];

                    $this->info("Importados: {$total} registros");
                }
            }

            // Importa o último lote, mesmo que tenha menos de 500 registros.
            if (!empty($lote)) {
                $quantidade = count($lote);

                DB::transaction(function () use (&$lote) {
                    DB::table('correlacoes_trib_nac_nbs')
                        ->insert($lote);
                });

                $total += $quantidade;
            }

            $this->newLine();
            $this->info("Importação concluída! Total: {$total} registros.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Erro na importação: ' . $e->getMessage());

            return self::FAILURE;
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }
}