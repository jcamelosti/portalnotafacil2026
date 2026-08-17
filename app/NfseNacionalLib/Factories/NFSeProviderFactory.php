<?php
namespace JCamelo\NfseNacionalLib\Factories;

use JCamelo\NfseNacionalLib\Providers\ISSNetProvider;

class NFSeProviderFactory
{
    public static function make(string $provider)
    {
        return match ($provider) {
            'issnet' => new ISSNetProvider(),
            //'betha' => new BethaProvider(),
            default => throw new \Exception("Provedor não suportado"),
        };
    }
}