<?php

namespace App\Console\Commands;

use App\Jobs\ExcluirEmpresaLicencaVencidaJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExcluirEmpresasLicencasVencidas extends Command
{
    protected $signature = 'empresas:excluir-licencas-vencidas
                            {--dias=90 : Quantidade de dias de vencimento}
                            {--dry-run : Apenas listar as empresas}';

    protected $description = 'Exclui empresas cujas últimas licenças venceram há mais de X dias';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');

        if ($dias < 1) {
            $this->error('A quantidade de dias deve ser maior que zero.');
            return self::FAILURE;
        }

        $dataLimite = Carbon::now()
            ->subDays($dias)
            ->toDateString();

        $this->info("Buscando empresas com última licença vencida antes de {$dataLimite}...");

        $empresas = DB::table('licenses')
            ->select('empresa_id')
            ->groupBy('empresa_id')
            ->havingRaw('MAX(`validate`) < ?', [$dataLimite])
            ->orderBy('empresa_id');

        $total = 0;

        $empresas->chunk(100, function ($registros) use ($dias, &$total) {

            foreach ($registros as $registro) {

                $total++;

                if ($this->option('dry-run')) {
                    $this->line("Empresa ID: {$registro->empresa_id}");
                    continue;
                }

                ExcluirEmpresaLicencaVencidaJob::dispatch(
                    $registro->empresa_id,
                    $dias
                );
            }
        });

        if ($this->option('dry-run')) {
            $this->warn("Simulação concluída. {$total} empresas encontradas.");
            return self::SUCCESS;
        }

        $this->info("Processamento iniciado. {$total} empresas enviadas para a fila.");

        return self::SUCCESS;
    }
}