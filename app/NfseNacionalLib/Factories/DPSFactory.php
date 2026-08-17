<?php

namespace JCamelo\NfseNacionalLib\Factories;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\XML\Builders\RegimeNormalBuilder;
use JCamelo\NfseNacionalLib\XML\Builders\SimplesNacionalBuilder;

class DPSFactory
{
    public static function make(DPSDataDTO $data): string
    {
        $builder = match ($data->opSimpNac) {
            3 => new SimplesNacionalBuilder(),
            1 => new RegimeNormalBuilder(),
            default => throw new \Exception("Regime tributário inválido"),
        };

        return $builder->build($data);
    }

    public static function makeRecepcionarLoteDpsSincrono(DPSDataDTO $data): string
    {
       $builder = match ($data->opSimpNac) {
            3 => new SimplesNacionalBuilder(),
            1 => new RegimeNormalBuilder(),
            default => throw new \Exception("Regime tributário inválido"),
        };

        return $builder->buildRecepcionarLoteDpsSincrono($data);
    }
}