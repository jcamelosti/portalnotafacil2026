<?php
namespace JCamelo\NfseNacionalLib\Contracts;

use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

interface DPSBuilderInterface
{
    public function build(DPSDataDTO $data): string;
}