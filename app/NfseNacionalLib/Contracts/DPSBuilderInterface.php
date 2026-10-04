<?php
namespace JCamelo\NfseNacionalLib\Contracts;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;

interface DPSBuilderInterface
{
    public function build(DPSDataSnDTO $data, string $xml): string;
}