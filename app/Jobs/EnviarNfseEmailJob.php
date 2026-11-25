<?php

namespace App\Jobs;

use App\Mail\NfseEmitidaMail;
use App\Models\NotaEmitida;
use App\Services\NotaFiscalServico\DanfsePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNfseEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /** Segundos de espera entre tentativas */
    public array $backoff = [60, 300, 900];

    /**
     * @param int         $notaId       ID da NotaEmitida
     * @param string|null $destinatario E-mail de destino (padrão: e-mail do tomador)
     */
    public function __construct(
        public int $notaId,
        public ?string $destinatario = null
    ) {}

    public function handle(DanfsePdfService $danfsePdfService): void
    {
        $nota = NotaEmitida::with(['empresa', 'tomador'])->find($this->notaId);

        if (!$nota) {
            Log::warning('EnviarNfseEmailJob: nota não encontrada.', ['nota_id' => $this->notaId]);
            return;
        }

        $email = $this->destinatario ?: optional($nota->tomador)->email;

        if (!$email) {
            Log::info('EnviarNfseEmailJob: nota sem e-mail de destino, envio ignorado.', ['nota_id' => $nota->id]);
            return;
        }

        if (empty($nota->nfse_xml)) {
            Log::warning('EnviarNfseEmailJob: nota sem XML, não é possível gerar o PDF.', ['nota_id' => $nota->id]);
            return;
        }

        // O PDF é gerado dentro do job (não serializar binário na fila)
        $pdf = $danfsePdfService->renderFromXml($nota->nfse_xml);

        Mail::to($email)->send(new NfseEmitidaMail($nota, $pdf));

        Log::info('EnviarNfseEmailJob: NFS-e enviada por e-mail.', [
            'nota_id' => $nota->id,
            'email'   => $email,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('EnviarNfseEmailJob: falha ao enviar NFS-e.', [
            'nota_id' => $this->notaId,
            'erro'    => $e->getMessage(),
        ]);
    }
}
