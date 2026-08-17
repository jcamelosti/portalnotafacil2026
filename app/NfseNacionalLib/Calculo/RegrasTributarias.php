<?php

namespace JCamelo\NfseNacionalLib\Calculo;

class RegrasTributarias
{
    public static function obter(int $ano): array
    {
        if ($ano === 2026) {
            return [
                'pIBSUF' => 0.10,
                'pIBSMun' => 0.00,
                'pCBS' => 0.90,
            ];
        }

        if ($ano === 2027 || $ano === 2028) {
            return [
                'pIBSUF' => 0.05,
                'pIBSMun' => 0.05,

                /*
                 * Não deixar CBS fixa aqui.
                 * Deve ser parametrizada.
                 */
                'pCBS' => null,
            ];
        }

        throw new \InvalidArgumentException(
            "Ano {$ano} não possui regras cadastradas."
        );
    }
}