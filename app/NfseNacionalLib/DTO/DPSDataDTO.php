<?php

namespace JCamelo\NfseNacionalLib\DTO;

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

        // 🔥 NOVOS CAMPOS
        public int $opSimpNac, // 1 ou 3
        public ?int $regApTribSN = null,

        public int $numDps
    ) {}
}