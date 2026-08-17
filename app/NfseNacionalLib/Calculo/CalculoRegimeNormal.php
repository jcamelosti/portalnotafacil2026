<?php

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\ResultadoCalculo;

class CalculoRegimeNormal
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

        $regras = RegrasTributarias::obter($ano);

        $vBC = $this->baseCalculo->calcular(
            $data,
            $ano
        );

        /*
         * Alíquotas
         */
        $pIBSUF = $data->pIBSUF
            ?? $regras['pIBSUF'];

        $pIBSMun = $data->pIBSMun
            ?? $regras['pIBSMun'];

        $pCBS = $data->pCBS
            ?? $regras['pCBS'];

        /*
         * IBS Estadual
         */
        $vIBSUF = round(
            $vBC * ($pIBSUF / 100),
            2
        );

        /*
         * IBS Municipal
         */
        $vIBSMun = round(
            $vBC * ($pIBSMun / 100),
            2
        );

        /*
         * IBS Total
         */
        $vIBSTot = round(
            $vIBSUF + $vIBSMun,
            2
        );

        /*
         * CBS
         */
        $vCBS = 0;

        if ($pCBS !== null) {
            $vCBS = round(
                $vBC * ($pCBS / 100),
                2
            );
        }

        return new ResultadoCalculo(
            vBC: $vBC,

            pIBSUF: $pIBSUF,
            vIBSUF: $vIBSUF,

            pIBSMun: $pIBSMun,
            vIBSMun: $vIBSMun,

            vIBSTot: $vIBSTot,

            pCBS: $pCBS ?? 0,
            vCBS: $vCBS
        );
    }
}