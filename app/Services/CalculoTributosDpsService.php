<?php

namespace App\Services;

use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;

class CalculoTributosDpsService
{
    public function calcular(DPSDataSnDTO $dto): array
    {
        // Valor inicial do serviço
        $valorServico = $this->numero($dto->valorServico);

        // Valor líquido inicial
        $valorLiquido = $valorServico;

        /*
         * ISSQN
         */
        $valorIssqn = 0;

        // Tipo de retenção ISS
        // Conforme sua regra JavaScript: diferente de 1 calcula e desconta.
        if ((int) $dto->tipoRetencaoIss !== 1) {

            $aliquotaIss = $this->numero($dto->aliquotaIss);

            if ($aliquotaIss > 0) {
                $valorIssqn = $valorServico * ($aliquotaIss / 100);
            }

            $valorIssqn = $this->arredondar($valorIssqn);

            $valorLiquido -= $valorIssqn;
        }

        /*
         * TRIBUTAÇÃO FEDERAL
         */

        $valorPis = 0;
        $valorCofins = 0;
        $somaTributosFederais = 0;

        $baseFederal = $this->numero(
            $dto->baseCalculoPisCofins
        );

        // PIS
        $aliquotaPis = $this->numero($dto->aliquotaPis);

        if ($aliquotaPis > 0) {
            $valorPis = $baseFederal * ($aliquotaPis / 100);
            $valorPis = $this->arredondar($valorPis);
        }

        // COFINS
        $aliquotaCofins = $this->numero($dto->aliquotaCofins);

        if ($aliquotaCofins > 0) {
            $valorCofins = $baseFederal * ($aliquotaCofins / 100);
            $valorCofins = $this->arredondar($valorCofins);
        }

        /*
         * PIS / COFINS RETIDOS
         *
         * Ajuste os códigos conforme a regra
         * de retenção utilizada no seu sistema.
         */

        $tipoRetencaoPisCofins = (int) $dto->tipoRetencaoPisCofins;

        if ($tipoRetencaoPisCofins > 0) {
            $somaTributosFederais += $valorPis;
            $somaTributosFederais += $valorCofins;
        }

        /*
         * CSLL
         */
        $valorCsll = $this->numero($dto->valorRetencaoCsll);

        if ($valorCsll > 0) {
            $somaTributosFederais += $valorCsll;
        }

        /*
         * IRRF
         */
        $valorIrrf = $this->numero($dto->valorRetencaoIrrf);

        $somaTributosFederais += $valorIrrf;

        /*
         * CONTRIBUIÇÃO PREVIDENCIÁRIA (CP)
         */
        $valorCp = $this->numero($dto->valorRetencaoCp);

        $somaTributosFederais += $valorCp;

        // Arredonda total de retenções federais
        $somaTributosFederais = $this->arredondar(
            $somaTributosFederais
        );

        // Desconta tributos federais do valor líquido
        $valorLiquido -= $somaTributosFederais;

        /*
         * RESULTADO
         */

        return [
            'valorServico' => $this->arredondar($valorServico),

            'valorIssqn' => $this->arredondar($valorIssqn),

            'valorPis' => $this->arredondar($valorPis),

            'valorCofins' => $this->arredondar($valorCofins),

            'valorCsll' => $this->arredondar($valorCsll),

            'valorIrrf' => $this->arredondar($valorIrrf),

            'valorCp' => $this->arredondar($valorCp),

            'totalTributosFederais' => $somaTributosFederais,

            'valorLiquido' => $this->arredondar($valorLiquido),
        ];
    }

    /**
     * Converte valores para número.
     */
    private function numero($valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', (string) $valor);
    }

    /**
     * Arredonda valores monetários para duas casas.
     */
    private function arredondar(float $valor): float
    {
        return round($valor, 2, PHP_ROUND_HALF_UP);
    }
}