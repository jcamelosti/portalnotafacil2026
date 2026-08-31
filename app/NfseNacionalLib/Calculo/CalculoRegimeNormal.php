<?php

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class CalculoRegimeNormal
{
    public function __construct(
        private BaseCalculoIBSCBS $baseCalculo
    ) {
    }

    public function calcular(
        DPSDataDTO $data
    ): ResultadoCalculo {

        $ano = (int) substr(
            $data->dataCompetencia,
            0,
            4
        );

        /*
         * 1. Regras do ano
         */
        $regras = RegrasTributarias::obter($ano);

        /*
         * 2. Base de cálculo
         */
        $vBC = $this->baseCalculo->calcular(
            $data,
            $ano
        );

        /*
         * 3. Alíquotas
         *
         * Se vierem no DTO, utiliza as informadas.
         * Caso contrário, utiliza as regras do ano.
         */
        $pIBSUF = $data->pIBSUF
            ?? $regras['pIBSUF'];

        $pIBSMun = $data->pIBSMun
            ?? $regras['pIBSMun'];

        $pCBS = $data->pCBS
            ?? $regras['pCBS'];

        /*
         * 4. Reduções
         */
        $pRedAliqUF = $data->pRedAliqUF ?? 0;
        $pRedAliqMun = $data->pRedAliqMun ?? 0;
        $pRedAliqCBS = $data->pRedAliqCBS ?? 0;

        $pRedutor = $data->pRedutor ?? 0;

        /*
         * 5. Alíquotas efetivas
         */
        $pAliqEfetUF = AliquotaEfetiva::calcular(
            $pIBSUF,
            $pRedAliqUF,
            $pRedutor
        );

        $pAliqEfetMun = AliquotaEfetiva::calcular(
            $pIBSMun,
            $pRedAliqMun,
            $pRedutor
        );

        $pAliqEfetCBS = 0;

        if ($pCBS !== null) {
            $pAliqEfetCBS = AliquotaEfetiva::calcular(
                $pCBS,
                $pRedAliqCBS,
                $pRedutor
            );
        }

        /*
         * 6. IBS Estadual
         */
        $vIBSUF = round(
            $vBC * ($pAliqEfetUF / 100),
            2
        );

        /*
         * 7. IBS Municipal
         */
        $vIBSMun = round(
            $vBC * ($pAliqEfetMun / 100),
            2
        );

        /*
         * 8. IBS Total
         */
        $vIBSTot = round(
            $vIBSUF + $vIBSMun,
            2
        );

        /*
         * 9. CBS
         */
        $vCBS = 0;

        if ($pCBS !== null) {
            $vCBS = round(
                $vBC * ($pAliqEfetCBS / 100),
                2
            );
        }

        /*
         * 10. Total da NF
         */
        $vTotNF = $ano >= 2027
            ? round(
                ($data->valorServico - $data->descIncond)
                + $vIBSTot
                + $vCBS,
                2
            )
            : round(
                $data->valorLiquido,
                2
            );

        /*
         * 11. Resultado
         */
        return new ResultadoCalculo(
            vBC: $vBC,

            pIBSUF: $pIBSUF,
            pAliqEfetUF: $pAliqEfetUF,
            vIBSUF: $vIBSUF,

            pIBSMun: $pIBSMun,
            pAliqEfetMun: $pAliqEfetMun,
            vIBSMun: $vIBSMun,

            vIBSTot: $vIBSTot,

            pCBS: $pCBS ?? 0,
            pAliqEfetCBS: $pAliqEfetCBS,
            vCBS: $vCBS,

            vTotNF: $vTotNF
        );
    }
}