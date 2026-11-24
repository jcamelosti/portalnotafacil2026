<?php

namespace App\Jobs;

use App\Mail\CertificadoVencidoMail;
use App\Models\Certificado;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExcluirCertificadoVencidoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public int $certificadoId
    ) {}

    public function handle(): void
    {
        $certificado = Certificado::find($this->certificadoId);

        if (!$certificado) {
            Log::warning('Certificado não encontrado para exclusão.', [
                'certificado_id' => $this->certificadoId,
            ]);
            return;
        }

        // Confere novamente se está vencido (pode ter sido renovado após o dispatch)
        if (!$certificado->data_validade || $certificado->data_validade->gte(Carbon::today())) {
            Log::info('Exclusão cancelada: certificado dentro da validade.', [
                'certificado_id' => $certificado->id,
                'data_validade' => optional($certificado->data_validade)->toDateString(),
            ]);
            return;
        }

        $empresa = Empresa::with('responsavel')->find($certificado->empresa_id);

        // Guarda os dados para o e-mail antes de excluir o registro
        $dados = [
            'razao_social' => $certificado->razao_social ?: optional($empresa)->razao_social,
            'cpf_cnpj' => optional($empresa)->cpf_cnpj,
            'data_validade' => $certificado->data_validade->format('d/m/Y'),
            'data_exclusao' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $this->excluirArquivo($certificado);

        $certificado->delete();

        Log::info('Certificado vencido excluído.', [
            'certificado_id' => $this->certificadoId,
            'empresa_id' => $certificado->empresa_id,
            'arquivo' => $certificado->arquivo,
        ]);

        $destinatarios = $this->destinatarios($empresa);

        if (empty($destinatarios)) {
            Log::warning('Certificado excluído sem destinatário para aviso por e-mail.', [
                'certificado_id' => $this->certificadoId,
                'empresa_id' => $certificado->empresa_id,
            ]);
            return;
        }

        Mail::to($destinatarios)->send(new CertificadoVencidoMail($dados));
    }

    /**
     * Remove o arquivo físico do certificado, desde que nenhum outro
     * certificado válido aponte para o mesmo arquivo (o nome é o CNPJ).
     */
    protected function excluirArquivo(Certificado $certificado): void
    {
        if (empty($certificado->arquivo)) {
            return;
        }

        $arquivoEmUso = Certificado::where('arquivo', $certificado->arquivo)
            ->where('id', '!=', $certificado->id)
            ->whereDate('data_validade', '>=', Carbon::today())
            ->exists();

        if ($arquivoEmUso) {
            Log::info('Arquivo mantido: utilizado por outro certificado válido.', [
                'certificado_id' => $certificado->id,
                'arquivo' => $certificado->arquivo,
            ]);
            return;
        }

        $caminhoBase = getenv('AMBIENTE_PRODUCAO') == 0
            ? getenv('CAMINHO_CERTIFICADO_LOCAL')
            : getenv('CAMINHO_CERTIFICADO_PROD');

        // basename evita que um valor malformado no banco aponte para fora da pasta
        $arquivo = basename($certificado->arquivo);

        // Os .pem são gerados como <CNPJ>_<tipo>.pem (ver NFePHP\Common\Certificado)
        $cnpj = pathinfo($arquivo, PATHINFO_FILENAME);

        $arquivos = [
            $arquivo,
            $cnpj . '_priKEY.pem',
            $cnpj . '_pubKEY.pem',
            $cnpj . '_certKEY.pem',
        ];

        foreach ($arquivos as $nome) {
            $caminho = $caminhoBase . $nome;

            if (!is_file($caminho)) {
                Log::warning('Arquivo do certificado não encontrado no disco.', [
                    'certificado_id' => $certificado->id,
                    'caminho' => $caminho,
                ]);
                continue;
            }

            if (!@unlink($caminho)) {
                // Lança exceção para o job ser reprocessado sem apagar o registro
                throw new \RuntimeException("Não foi possível excluir o arquivo do certificado: {$caminho}");
            }
        }
    }

    protected function destinatarios(?Empresa $empresa): array
    {
        if (!$empresa) {
            return [];
        }

        $emails = [
            $empresa->email,
            optional($empresa->responsavel)->email,
        ];

        return array_values(array_unique(array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        })));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao excluir certificado vencido.', [
            'certificado_id' => $this->certificadoId,
            'erro' => $exception->getMessage(),
        ]);
    }
}
