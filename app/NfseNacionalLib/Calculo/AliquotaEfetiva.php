<?php

namespace JCamelo\NfseNacionalLib\Calculo;

class AliquotaEfetiva
{
    public static function calcular(
        float $aliquota,
        float $reducao = 0,
        float $redutor = 0
    ): float {

        return round(
            $aliquota
            * (1 - ($reducao / 100))
            * (1 - ($redutor / 100)),
            2
        );
    }
}