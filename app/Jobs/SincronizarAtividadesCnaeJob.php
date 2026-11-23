<?php

namespace App\Jobs;

use App\Models\Empresa;
use App\Services\Cnaes\CnaesApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Busca na API de CNAEs os itens da LC 116 de cada CNAE da empresa
 * e os cadastra em empresa_atividades.
 */
class SincronizarAtividadesCnaeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public array $backoff = [30, 120];

    public function __construct(
        public int $empresaId
    ) {
    }

    public function handle(CnaesApiService $service): void
    {
        $empresa = Empresa::find($this->empresaId);

        if (!$empresa) {
            return;
        }

        $total = $service->sincronizarAtividades($empresa);

        Log::info("API CNAEs: {$total} atividade(s) sincronizada(s) para a empresa {$empresa->id}.");
    }

    public function failed(Throwable $e): void
    {
        Log::error('API CNAEs: falha ao sincronizar atividades', [
            'empresa_id' => $this->empresaId,
            'erro' => $e->getMessage(),
        ]);
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("atividades-cnae-{$this->empresaId}"))
                ->releaseAfter(30)
                ->expireAfter(180),
        ];
    }
}
