<?php

namespace JCamelo\NfseNacionalLib\DTO;

class EventoDTO
{
    public function __construct(
        public string $xNome,
        public string $dataInicio,
        public string $dataFim,
        public EnderecoDTO $end,
    ) {
    }

    public function toArray(): array
    {
        return [
            'xNome' => $this->xNome,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'end' => $this->end?->toArray(),
        ];
    }
}