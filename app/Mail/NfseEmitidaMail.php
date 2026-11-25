<?php

namespace App\Mail;

use App\Models\NotaEmitida;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class NfseEmitidaMail extends Mailable
{
    use Queueable;

    public NotaEmitida $nota;
    private string $pdf;

    /**
     * @param NotaEmitida $nota
     * @param string $pdf Conteúdo binário do PDF (retorno do DanfsePdfService)
     */
    public function __construct(NotaEmitida $nota, string $pdf)
    {
        $this->nota = $nota;
        $this->pdf  = $pdf;
    }

    public function build()
    {
        $numero = $this->nota->num_nfse ?: $this->nota->id;

        $mail = $this
            ->subject("NFS-e nº {$numero} - " . ($this->nota->empresa->razao_social ?? ''))
            ->view('emails.nfse.emitida', ['nota' => $this->nota])
            ->attachData($this->pdf, "NFSe_{$numero}.pdf", [
                'mime' => 'application/pdf',
            ]);

        // Anexa também o XML da nota, se existir
        if (!empty($this->nota->nfse_xml)) {
            $mail->attachData($this->nota->nfse_xml, "NFSe_{$numero}.xml", [
                'mime' => 'application/xml',
            ]);
        }

        return $mail;
    }
}
