<?php

namespace App\Console\Commands;

use App\Jobs\ExcluirCertificadoVencidoJob;
use App\Models\Certificado;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExcluirCertificadosVencidos extends Command
{
    protected $signature = 'certificados:excluir-vencidos
                            {--dry-run : Apenas listar os certificados vencidos}';

    protected $description = 'Exclui arquivo e registro dos certificados digitais vencidos e avisa por e-mail';

    public function handle(): int
    {
        $hoje = Carbon::today()->toDateString();

        $this->info("Buscando certificados com validade anterior a {$hoje}...");

        $total = 0;

        Certificado::whereDate('data_validade', '<', $hoje)
            ->orderBy('id')
            ->chunkById(100, function ($certificados) use (&$total) {
                foreach ($certificados as $certificado) {
                    $total++;

                    if ($this->option('dry-run')) {
                        $this->line("Certificado ID: {$certificado->id} | Empresa ID: {$certificado->empresa_id} | Validade: {$certificado->data_validade->format('d/m/Y')} | Arquivo: {$certificado->arquivo}");
                        continue;
                    }

                    ExcluirCertificadoVencidoJob::dispatch($certificado->id);
                }
            });

        if ($this->option('dry-run')) {
            $this->warn("Simulação concluída. {$total} certificados encontrados.");
            return self::SUCCESS;
        }

        $this->info("Processamento iniciado. {$total} certificados enviados para a fila.");

        return self::SUCCESS;
    }
}
