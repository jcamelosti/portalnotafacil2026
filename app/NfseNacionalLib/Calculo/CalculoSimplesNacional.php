<?php

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\ResultadoCalculo;

class CalculoSimplesNacional
{
    public function __construct(
        private BaseCalculoIBSCBS $baseCalculo
    ) {}

    public function calcular(
        DPSDataDTO $data
    ): ResultadoCalculo {

        $ano = (int) substr(
            $data->dataCompetencia,
            0,
            4
        );

        $vBC = $this->baseCalculo->calcular(
            $data,
            $ano
        );

        $pIBSSN = $data->pIBSSN ?? 0;
        $pCBSSN = $data->pCBSSN ?? 0;

        $vIBSSN = round(
            $vBC * ($pIBSSN / 100),
            2
        );

        $vCBSSN = round(
            $vBC * ($pCBSSN / 100),
            2
        );

        return new ResultadoCalculo(
            vBC: $vBC,

            pIBSSN: $pIBSSN,
            vIBSSN: $vIBSSN,

            pCBSSN: $pCBSSN,
            vCBSSN: $vCBSSN
        );
    }
}