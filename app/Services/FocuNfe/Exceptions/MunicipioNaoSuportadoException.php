
<?php

namespace App\Services\FocuNfe\Exceptions;

use RuntimeException;

class MunicipioNaoSuportadoException extends RuntimeException
{
    public static function paraCodigo(string $codigo): self
    {
        return new self(
            "Integração Focunfse não implementada para o município IBGE: {$codigo}"
        );
    }
}
