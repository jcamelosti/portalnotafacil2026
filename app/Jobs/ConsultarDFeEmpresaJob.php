<?php

namespace App\Jobs;

use App\Models\Certificado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Models\DfeConsulta;
use App\Models\DocumentoFiscalRecebido;
use App\Models\Empresa;
use App\Services\Nfse\DanfseXmlParser;
use Carbon\Carbon;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConsultarDFeEmpresaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 120;

    public array $backoff = [10, 30, 60, 120];


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public int $empresaId
    ) {
        $this->onQueue('fiscal');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        DanfseXmlParser $parser
    ): void {
        $empresa = Empresa::findOrFail($this->empresaId);

        /*
         * Impede duas consultas simultâneas da mesma empresa.
         * A consulta seguinte também será serializada pela fila.
         */
        $consulta = DfeConsulta::firstOrCreate(
            ['empresa_id' => $empresa->id],
            [
                'ult_nsu' => 0,
                'max_nsu' => 0,
                'status' => 'pendente',
            ]
        );

        if ($consulta->status === 'concluida') {
            return;
        }

        $consulta->update([
            'status' => 'processando',
            'iniciada_em' => $consulta->iniciada_em ?? now(),
            'erro' => null,
        ]);

        try {
            /*
             * IMPORTANTE:
             * Este método precisa consultar a partir do NSU salvo.
             *
             * Se a sua implementação atual recebe somente dois
             * parâmetros, adapte-a para aceitar o cursor ou garanta
             * que ela já o controla internamente.
             */
            $empresa = Empresa::find(1);
            $certificadoCliente = Certificado::where('empresa_id', $empresa->id)
                ->first();
            //$certificado = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
            if(getenv("AMBIENTE_PRODUCAO") == 0){
                //desenvolvimento
                $certificado = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
            }else{
                $certificado = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
            }

            $config = new \stdClass();
            $config->tpamb = 1; //1 - Produção, 2 - Homologação
            //$config->formatOutput = true; // Para debug retorna XML formatado
            $configJson = json_encode($config);

            $content = file_get_contents($certificado);
            $password = base64_decode($certificadoCliente->senha);
           
            $cert = \NFePHP\Common\Certificate::readPfx($content, $password);
            $tools = new \Hadder\NfseNacional\Tools($configJson, $cert);

            $resposta = $tools->consultaDocumentosFiscaisServico(
                $consulta->ult_nsu,
                $empresa->cpf_cnpj,
            );

            if (isset($resposta['erro'])) {
                throw new \RuntimeException(
                    is_string($resposta['erro'])
                        ? $resposta['erro']
                        : json_encode($resposta['erro'])
                );
            }

            $lote = $resposta['LoteDFe'] ?? [];

            if (!is_array($lote)) {
                throw new \RuntimeException(
                    'Formato inválido no lote de documentos fiscais.'
                );
            }

            $nsuAnterior = (int) $consulta->ult_nsu;
            $maiorNsuLote = $nsuAnterior;

            foreach ($lote as $nota) {
                $nsu = (int) ($nota['NSU'] ?? 0);

                if ($nsu > $maiorNsuLote) {
                    $maiorNsuLote = $nsu;
                }

                if (($nota['TipoDocumento'] ?? '') === 'EVENTO') {
                    continue;
                }

                $xml = $nota['ConteudoXml'] ?? null;

                if (!is_string($xml) || trim($xml) === '') {
                    continue;
                }

                $dados = $parser->parseDFe($xml);

                $cnpjTomador = preg_replace(
                    '/\D/',
                    '',
                    (string) ($dados['tomador']['cnpj'] ?? '')
                );

                $cnpjEmpresa = preg_replace(
                    '/\D/',
                    '',
                    (string) $empresa->cpf_cnpj
                );

                // Mantém apenas NFS-e em que a empresa é tomadora.
                if ($cnpjTomador !== $cnpjEmpresa) {
                    continue;
                }

                DocumentoFiscalRecebido::updateOrCreate(
                    [
                        'empresa_id' => $empresa->id,
                        'nsu' => $nsu,
                    ],
                    [
                        'tipo_documento' => 'NFSE',
                        'xml' => $xml,
                        'dados' => $dados,
                        'doc_prestador' => $dados['prestador']['cnpj'],
                        'razao_social' => $dados['prestador']['nome'],
                        'data_emissao_nfse' => Carbon::createFromFormat(
                            'd/m/Y H:i:s',
                            $dados['header']['emissao_nfse']
                        )->format('Y-m-d')
                    ]
                );
            }

            /*
             * Prefira os metadados oficiais da distribuição, quando
             * a resposta os fornecer. Não confunda o maior NSU do
             * lote com maxNSU da distribuição.
             */
            $novoNsu = max(
                $maiorNsuLote,
                (int) ($resposta['ultNSU'] ?? $nsuAnterior)
            );

            $maxNsu = max(
                (int) $consulta->max_nsu,
                (int) ($resposta['maxNSU'] ?? 0)
            );

            if (empty($lote)) {
                $consulta->update([
                    'ult_nsu' => $novoNsu,
                    'max_nsu' => $maxNsu,
                    'status' => 'concluida',
                    'finalizada_em' => now(),
                ]);

                return;
            }

            if ($novoNsu <= $nsuAnterior) {
                throw new \RuntimeException(
                    'O NSU não avançou. Consulta interrompida para evitar loop.'
                );
            }

            $consulta->update([
                'ult_nsu' => $novoNsu,
                'max_nsu' => $maxNsu,
                'status' => 'pendente',
                'erro' => null,
            ]);

            // Agenda somente o próximo lote, com intervalo entre chamadas.
            self::dispatch($empresa->id)->delay(now()->addSeconds(2));
        } catch (Throwable $e) {
            $consulta->update([
                'status' => 'erro',
                'erro' => mb_substr($e->getMessage(), 0, 60000),
            ]);

            Log::error('Falha na consulta de DF-e', [
                'empresa_id' => $empresa->id,
                'nsu' => $consulta->ult_nsu,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("dfe-empresa-{$this->empresaId}"))
                ->releaseAfter(10)
                ->expireAfter(180),
        ];
    }
}
