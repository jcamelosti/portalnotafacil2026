<?php

namespace JCamelo\NfseNacionalLib\DTO;

class ImovelDataDTO
{
    public function __construct(
        public ?string $inscImobFisc = null,
        //public ?string $nProcessoObra = null,
        public ?EnderecoImovelDTO $end = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'inscImobFisc' => $this->inscImobFisc,
            //'nProcessoObra' => $this->nProcessoObra,
            'end' => $this->end?->toArray(),
        ];
    }
}