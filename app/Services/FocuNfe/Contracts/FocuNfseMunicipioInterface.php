<?php
namespace App\Services\FocuNfe\Contracts;

use App\Models\Empresa;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;

interface FocunfseMunicipioInterface
{
    /**
     * Gera o JSON específico do município.
     *
     * @param Empresa $empresa
     * @param array $dados
     * @return array
     */
    public function gerarPayload(
        Empresa $empresa,
        DPSDataSnDTO $dados
    ): array;
}
