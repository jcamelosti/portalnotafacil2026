<?php
namespace App\Services\FocuNfe\Factories;

use App\Models\Empresa;
use App\Services\FocuNfe\Contracts\FocunfseMunicipioInterface;
use App\Services\FocuNfe\Exceptions\MunicipioNaoSuportadoException;
use InvalidArgumentException;

class FocunfseFactory
{
    /**
     * Identifica e instancia a implementação do município.
     */
    public function make(
        Empresa $empresa
    ): FocunfseMunicipioInterface {

        $codigoIbge = $empresa->cidade_id;

        if (!$codigoIbge) {
            throw new InvalidArgumentException(
                'A empresa não possui um município com código IBGE informado.'
            );
        }

        $codigoIbge = preg_replace(
            '/\D/',
            '',
            (string) $codigoIbge
        );

        $classe = $this->resolverClasse($codigoIbge);

        if (!class_exists($classe)) {
            throw MunicipioNaoSuportadoException::paraCodigo(
                $codigoIbge
            );
        }

        $municipio = app($classe);

        if (!$municipio instanceof FocunfseMunicipioInterface) {
            throw new InvalidArgumentException(
                "A classe {$classe} não implementa a interface obrigatória."
            );
        }
        
        return $municipio;
    }

    /**
     * Resolve a classe através do código IBGE.
     */
    private function resolverClasse(string $codigoIbge): string
    {
        return sprintf(
            'App\\Services\\FocuNfe\Municipios\\Municipio%s',
            $codigoIbge
        );
    }
}