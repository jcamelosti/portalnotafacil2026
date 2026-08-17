<?php

namespace JCamelo\NfseNacionalLib\DTO;

/*class DPSDataDTO
{
    public function __construct(
        public string $cnpjPrestador,
        public string $imPrestador,
        public string $cnpjTomador,
        public string $razaoTomador,
        public string $codigoMunicipio,
        public string $codigoTributacaoNacional,
        public string $codigoServico,
        public string $descricaoServico,
        public float $valorServico,
        public string $dataCompetencia,

        // 🔥 NOVOS CAMPOS
        public int $opSimpNac, // 1 ou 3
        public ?int $regApTribSN,

        public int $numDps
    ) {}
}*/

class DPSDataDTO
{
    public function __construct(
        public string $cnpjPrestador,
        public string $imPrestador,

        public string $cnpjTomador,
        public string $razaoTomador,

        public string $codigoMunicipio,
        public string $codigoTributacaoNacional,
        public string $codigoServico,
        public string $descricaoServico,

        public float $valorServico,

        public string $dataCompetencia,

        /*
          Ambiente Emissão
          1 - Produção
          2 - Homologação      
        */
        public int $ambiente_emissao,

        /*
         * Regime tributário
         *
         * 1 = Não optante
         * 2 = MEI
         * 3 = Simples Nacional ME/EPP
         * 4 = Optante pendente
         */
        public int $opSimpNac,

        /*
         * Regime de apuração IBS/CBS
         *
         * 1 = IBS e CBS pelo Simples
         * 2 = CBS pelo Simples / IBS regular
         * 3 = IBS e CBS pelo regime regular
         */
        public ?int $regApIBSCBSSN = null,

        /*
         * Tributação IBS/CBS
         */
        public string $cst = '000',
        public string $cClassTrib = '000001',
        public string $cIndOp = '050103',

        /*
         * Para operações de consumo pessoal
         */
        public int $indFinal = 0,

        /*
         * Destinatário
         */
        public int $indDest = 0,

        /*
         * Número da DPS
         */
        public int $numDps = 1,

        /*
         * Alíquotas
         *
         * Se não forem informadas,
         * serão determinadas pelo cálculo do ano/regime.
         */
        public ?float $pIBSUF = null,
        public ?float $pIBSMun = null,
        public ?float $pCBS = null,

        /*
         * Simples Nacional
         */
        public ?float $pIBSSN = null,
        public ?float $pCBSSN = null,

        /*
         * Deduções da base
         */
        public float $descIncond = 0,
        public float $vISSQN = 0,
        public float $vPIS = 0,
        public float $vCOFINS = 0,

        /*
         * Ajustes da base
         */
        public float $vCalcAjusteBCIBSCBS = 0,
        public float $vCalcAjusteBCLocImoveis = 0,
    ) {}
}