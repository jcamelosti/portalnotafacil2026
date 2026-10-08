<?php

namespace JCamelo\NfseNacionalLib\DTO;

class EnderecoObraDTO
{
    public function __construct(
        // Endereço nacional
        public ?string $cMun = null,
        public ?string $CEP = null,

        // Endereço exterior
        public ?string $cPais = null,
        public ?string $cEndPost = null,
        public ?string $xCidade = null,
        public ?string $xEstProvReg = null,

        // Dados do logradouro
        public ?string $xLgr = null,
        public ?string $nro = null,
        public ?string $xCpl = null,
        public ?string $xBairro = null,
    ) {
    }

    public function isNacional(): bool
    {
        return !empty($this->cMun);
    }

    public function isExterior(): bool
    {
        return !empty($this->cPais);
    }

    public function toArray(): array
    {
        return [
            'endNac' => [
                'cMun' => $this->cMun,
                'CEP' => $this->CEP,
            ],
            'endExt' => [
                'cPais' => $this->cPais,
                'cEndPost' => $this->cEndPost,
                'xCidade' => $this->xCidade,
                'xEstProvReg' => $this->xEstProvReg,
            ],
            'xLgr' => $this->xLgr,
            'nro' => $this->nro,
            'xCpl' => $this->xCpl,
            'xBairro' => $this->xBairro,
        ];
    }
}