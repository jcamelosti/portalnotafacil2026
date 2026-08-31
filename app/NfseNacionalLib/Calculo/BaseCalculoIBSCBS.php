<?php

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class BaseCalculoIBSCBS
{
    public function calcular(
        DPSDataDTO $data,
        int $ano
    ): float {

        $base = $data->valorServico;

        // Desconto incondicionado
        $base -= $data->descIncond;

        // Ajustes da base IBS/CBS
        $base -= $data->vCalcAjusteBCIBSCBS;

        // Ajustes relacionados a imóveis
        $base -= $data->vCalcAjusteBCLocImoveis;

        // ISSQN
        $base -= $data->vISSQN;

        /*
         * Até 2026:
         *
         * PIS e COFINS também reduzem
         * a base de cálculo do IBS/CBS.
         *
         * A partir de 2027:
         *
         * não são mais deduzidos.
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