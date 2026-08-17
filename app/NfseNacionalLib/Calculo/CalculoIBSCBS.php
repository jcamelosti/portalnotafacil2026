<?php

namespace JCamelo\NfseNacionalLib\Calculo;
use JCamelo\NfseNacionalLib\DTO\DadosTributacao;

class CalculoIBSCBS
{
    public function __construct(
        private BaseCalculoIBSCBS $baseCalculo,
        private CalculoRegimeNormal $regimeNormal,
        private CalculoSimplesNacional $simples
    ) {}

    public function calcular(
        DadosTributacao $dados,
        int $ano,
        string $regime,
        int $regApIBSCBSSN = 3
    ): ResultadoCalculo {

        if ($regime === 'normal') {
            return $this->regimeNormal->calcular(
                $dados,
                $ano
            );
        }

        if ($regime === 'simples') {

            return match ($regApIBSCBSSN) {

                // IBS + CBS pelo Simples
                1 => $this->simples->calcular(
                    $dados,
                    $ano
                ),

                // IBS regular + CBS Simples
                2 => $this->calcularIBSRegularCBSSimples(
                    $dados,
                    $ano
                ),

                // IBS + CBS regime regular
                3 => $this->regimeNormal->calcular(
                    $dados,
                    $ano
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

    private function calcularIBSRegularCBSSimples(
        DadosTributacao $dados,
        int $ano
    ): ResultadoCalculo {

        $normal = $this->regimeNormal->calcular(
            $dados,
            $ano
        );

        $simples = $this->simples->calcular(
            $dados,
            $ano
        );

        return new ResultadoCalculo(
            vBC: $normal->vBC,

            pIBSUF: $normal->pIBSUF,
            pAliqEfetUF: $normal->pAliqEfetUF,
            vIBSUF: $normal->vIBSUF,

            pIBSMun: $normal->pIBSMun,
            pAliqEfetMun: $normal->pAliqEfetMun,
            vIBSMun: $normal->vIBSMun,

            vIBSTot: $normal->vIBSTot,

            pCBSSN: $simples->pCBSSN,
            vCBSSN: $simples->vCBSSN,

            vTotNF: $ano >= 2027
                ? round(
                    ($dados->vServ - $dados->descIncond)
                    + $normal->vIBSTot
                    + $simples->vCBSSN,
                    2
                )
                : ($dados->vServ - $dados->descIncond)
        );
    }
}