<?php

namespace JCamelo\NfseNacionalLib\Calculo;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class CalculoIBSCBS
{
    public function __construct(
        private CalculoRegimeNormal $regimeNormal,
        private CalculoSimplesNacional $simples
    ) {
    }

    public function calcular(
        DPSDataDTO $data,
        string $regime,
        int $regApIBSCBSSN = 3
    ): ResultadoCalculo {

        /*
         * Regime Normal
         */
        if ($regime === 'normal') {
            return $this->regimeNormal->calcular(
                $data
            );
        }

        /*
         * Simples Nacional
         */
        if ($regime === 'simples') {

            return match ($regApIBSCBSSN) {

                /*
                 * IBS + CBS pelo Simples
                 */
                1 => $this->simples->calcular(
                    $data
                ),

                /*
                 * IBS regular + CBS Simples
                 */
                2 => $this->calcularIBSRegularCBSSimples(
                    $data
                ),

                /*
                 * IBS + CBS regime regular
                 */
                3 => $this->regimeNormal->calcular(
                    $data
                ),

                default => throw new \InvalidArgumentException(
                    'regApIBSCBSSN inválido.'
                ),
            };
        }

        throw new \InvalidArgumentException(
            'Regime tributário inválido.'
        );
    }

    /**
     * IBS no regime regular
     * CBS pelo Simples Nacional
     */
    private function calcularIBSRegularCBSSimples(
        DPSDataDTO $data
    ): ResultadoCalculo {

        $normal = $this->regimeNormal->calcular(
            $data
        );

        $simples = $this->simples->calcular(
            $data
        );

        return new ResultadoCalculo(

            /*
             * Base
             */
            vBC: $normal->vBC,

            /*
             * IBS UF
             */
            pIBSUF: $normal->pIBSUF,
            pAliqEfetUF: $normal->pAliqEfetUF,
            vIBSUF: $normal->vIBSUF,

            /*
             * IBS Município
             */
            pIBSMun: $normal->pIBSMun,
            pAliqEfetMun: $normal->pAliqEfetMun,
            vIBSMun: $normal->vIBSMun,

            /*
             * IBS total
             */
            vIBSTot: $normal->vIBSTot,

            /*
             * CBS Simples
             */
            pCBSSN: $simples->pCBSSN,
            vCBSSN: $simples->vCBSSN,

            /*
             * Total NF
             */
            vTotNF: $this->calcularTotalNF(
                $data,
                $normal->vIBSTot,
                $simples->vCBSSN
            )
        );
    }

    private function calcularTotalNF(
        DPSDataDTO $data,
        float $vIBSTot,
        float $vCBS
    ): float {

        $ano = (int) substr(
            $data->dataCompetencia,
            0,
            4
        );

        if ($ano <= 2026) {
            return round(
                $data->valorLiquido,
                2
            );
        }

        return round(
            ($data->valorServico - $data->descIncond)
            + $vIBSTot
            + $vCBS,
            2
        );
    }
}