<?php

namespace JCamelo\NfseNacionalLib\DTO;

class ObraDataDTO
{
    public function __construct(
        public ?string $inscImobFisc = null,
        public ?string $nProcessoObra = null,
        public ?EnderecoObraDTO $end = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'inscImobFisc' => $this->inscImobFisc,
            'nProcessoObra' => $this->nProcessoObra,
            'end' => $this->end?->toArray(),
        ];
    }
}