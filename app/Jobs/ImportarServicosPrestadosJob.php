<?php

namespace App\Jobs;

use App\Models\Empresa;
use App\Services\EmissorNotaService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Importa as NFS-e de serviços prestados (ISSNet) dos últimos N dias,
 * consultando o provedor em janelas de 10 dias.
 */
class ImportarServicosPrestadosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Quantidade de dias para trás a partir de hoje */
    public const DIAS_RETROATIVOS = 60;

    /** Tamanho de cada janela de consulta (dias, inclusivo) */
    public const DIAS_POR_CONSULTA = 10;

    public int $tries = 3;

    public int $timeout = 900;

    public array $backoff = [30, 120];

    public function __construct(
        public int $empresaId
    ) {
        $this->onQueue('fiscal');
    }

    public function handle(EmissorNotaService $service): void
    {
        $empresa = Empresa::find($this->empresaId);

        if (is_null($empresa)) {
            Log::warning('ImportarServicosPrestadosJob: empresa não encontrada', ['empresa_id' => $this->empresaId]);
            return;
        }

        $hoje   = Carbon::today();
        $inicio = $hoje->copy()->subDays(self::DIAS_RETROATIVOS);

        $importadas = 0;
        $ignoradas  = 0;
        $erros      = [];

        while ($inicio->lte($hoje)) {
            $fim = $inicio->copy()->addDays(self::DIAS_POR_CONSULTA - 1);
            if ($fim->gt($hoje)) {
                $fim = $hoje->copy();
            }

            $periodo = $inicio->format('Y-m-d') . ' a ' . $fim->format('Y-m-d');

            try {
                $retorno = $service->consultarServicosPrestados([
                    'data_inicio' => $inicio->format('Y-m-d'),
                    'data_fim'    => $fim->format('Y-m-d'),
                ], $empresa);

                if ($retorno['error']) {
                    $erros[] = $periodo . ': ' . $retorno['message'];
                } else {
                    $importadas += count($retorno['data'] ?? []);
                    $ignoradas  += $retorno['ignoradas'] ?? 0;
                }
            } catch (Throwable $e) {
                // Falha de comunicação: registra e segue para a próxima janela
                $erros[] = $periodo . ': ' . $e->getMessage();
            }

            $inicio = $fim->copy()->addDay();

            // Pequeno intervalo entre as chamadas ao provedor
            if ($inicio->lte($hoje)) {
                sleep(2);
            }
        }

        Log::info('ImportarServicosPrestadosJob concluído', [
            'empresa_id' => $empresa->id,
            'importadas' => $importadas,
            'ignoradas'  => $ignoradas,
            'erros'      => $erros,
        ]);
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("servicos-prestados-empresa-{$this->empresaId}"))
                ->releaseAfter(60)
                ->expireAfter($this->timeout + 60),
        ];
    }
}
