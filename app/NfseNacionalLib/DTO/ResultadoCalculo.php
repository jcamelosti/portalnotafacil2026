<?php

namespace JCamelo\NfseNacionalLib\DTO;

class ResultadoCalculo
{
    public function __construct(
        public float $vBC = 0,

        public float $pIBSUF = 0,
        public float $vIBSUF = 0,

        public float $pIBSMun = 0,
        public float $vIBSMun = 0,

        public float $vIBSTot = 0,

        public float $pCBS = 0,
        public float $vCBS = 0,

        /*
         * Simples Nacional
         */
        public float $pIBSSN = 0,
        public float $vIBSSN = 0,

        public float $pCBSSN = 0,
        public float $vCBSSN = 0,
    ) {}
}