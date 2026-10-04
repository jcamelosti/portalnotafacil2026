<?php

namespace JCamelo\NfseNacionalLib\Factories;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\XML\Builders\RegimeNormalBuilder;
use JCamelo\NfseNacionalLib\XML\Builders\SimplesNacionalBuilder;

class DPSFactory
{
    /*public static function make(DPSDataDTO $data): string
    {
        $builder = match ($data->opSimpNac) {
            3 => new SimplesNacionalBuilder(),
            1 => new RegimeNormalBuilder(),
            default => throw new \Exception("Regime tributário inválido"),
        };

        return $builder->build($data);
    }*/

    /*public static function makeRecepcionarLoteDpsSincrono(DPSDataDTO $data): string
    {
       $builder = match ($data->opSimpNac) {
            3 => new SimplesNacionalBuilder(),
            1 => new RegimeNormalBuilder(),
            default => throw new \Exception("Regime tributário inválido"),
        };

        return $builder->buildRecepcionarLoteDpsSincrono($data);
    }*/

    /* Criada dia 08/09/2026 */
    public static function make(DPSDataSnDTO $data, string $xml): string
    {
        $builder = match ($data->opSimpNac) {
            3 => new SimplesNacionalBuilder(),
            1 => new RegimeNormalBuilder(),
            default => throw new \Exception("Regime tributário inválido"),
        };

        return $builder->build($data, $xml);
    }
}