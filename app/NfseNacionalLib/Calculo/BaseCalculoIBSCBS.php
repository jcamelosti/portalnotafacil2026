<?php

namespace JCamelo\NfseNacionalLib\Calculo;
/*use JCamelo\NfseNacionalLib\DTO\DadosTributacao;

class BaseCalculoIBSCBS
{
    public function calcular(
        DadosTributacao $dados,
        int $ano
    ): float {

        $base = $dados->vServ;

        $base -= $dados->descIncond;

        $base -= $dados->vCalcAjusteBCIBSCBS;

        $base -= $dados->vCalcAjusteBCLocImoveis;

        $base -= $dados->vISSQN;

        if ($ano <= 2026) {
            $base -= $dados->vPIS;
            $base -= $dados->vCOFINS;
        }

        return max(0, round($base, 2));
    }
}*/

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class BaseCalculoIBSCBS
{
    public function calcular(
        DPSDataDTO $data,
        int $ano
    ): float {

        $base = $data->valorServico;

        $base -= $data->descIncond;

        $base -= $data->vCalcAjusteBCIBSCBS;

        $base -= $data->vCalcAjusteBCLocImoveis;

        $base -= $data->vISSQN;

        /*
         * Até 2026:
         *
         * também deduz PIS e COFINS.
         *
         * A partir de 2027:
         * não deduz mais.
         */
        if ($ano <= 2026) {
            $base -= $data->vPIS;
            $base -= $data->vCOFINS;
        }

        return max(
            0,
            round($base, 2)
        );
    }
}