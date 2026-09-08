<?php
namespace JCamelo\NfseNacionalLib\DTO;

use Carbon\Carbon;

class DPSDataSnDTO
{
    public function __construct(

        // DPS
        public readonly int $ambiente,
        public readonly string $dataEmissao,
        public readonly string $serie,
        public readonly int $numDps,
        public readonly string $dataCompetencia,
        public readonly string $codigoMunicipio,

        // Prestador
        public readonly string $cnpjPrestador,
        public readonly ?string $imPrestador = null,
        public readonly ?string $fonePrestador = null,
        public readonly ?string $emailPrestador = null,

        // Regime tributário
        public readonly int $opSimpNac = 1,
        public readonly ?int $regApTribSN = null,
        public readonly int $regEspTrib = 0,

        // Tomador
        public readonly ?string $cnpjTomador = null,
        public readonly ?string $cpfTomador = null,
        public readonly ?string $razaoTomador = null,

        // Endereço tomador
        public readonly ?string $codigoMunicipioTomador = null,
        public readonly ?string $cepTomador = null,
        public readonly ?string $logradouroTomador = null,
        public readonly ?string $numeroTomador = null,
        public readonly ?string $complementoTomador = null,
        public readonly ?string $bairroTomador = null,
        public readonly ?string $foneTomador = null,
        public readonly ?string $emailTomador = null,

        // Serviço
        public readonly string $codigoTributacaoNacional,
        public readonly ?string $codigoServicoMunicipal = null,
        public readonly string $descricaoServico = '',
        public readonly ?string $codigoNbs = null,
        public readonly ?string $codigoMunicipioPrestacao = null,

        // Valores
        public readonly string $valorServico = '0.00',

        // ISS
        public readonly ?int $tributaIss = null,
        public readonly ?int $tipoRetencaoIss = null,
        public readonly ?string $aliquotaIss = null,

        // PIS/COFINS
        public readonly ?string $cstPisCofins = null,
        public readonly ?int $tipoRetencaoPisCofins = null,

        // Retenções
        public readonly ?string $valorRetencaoCp = null,
        public readonly ?string $valorRetencaoIrrf = null,

        // Total tributos
        public readonly ?string $percentualTotalTributos = null,

        // IBS/CBS
        public readonly ?int $finNfse = null,
        public readonly ?string $cIndOp = null,
        public readonly ?int $indDest = null,
        public readonly ?string $cstIbsCbs = null,
        public readonly ?string $cClassTrib = null,

        // Versão
        public readonly string $versao = '1.01',
        public readonly string $verAplic = '1.01',
    ) {}
}
