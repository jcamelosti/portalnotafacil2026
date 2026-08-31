<?php

namespace JCamelo\NfseNacionalLib\Calculo;

class ResultadoCalculo
{
    public function __construct(

        /*
         * Base
         */
        public float $vBC,

        /*
         * IBS Estadual
         */
        public float $pIBSUF = 0,
        public float $pAliqEfetUF = 0,
        public float $vIBSUF = 0,

        /*
         * IBS Municipal
         */
        public float $pIBSMun = 0,
        public float $pAliqEfetMun = 0,
        public float $vIBSMun = 0,

        /*
         * IBS Total
         */
        public float $vIBSTot = 0,

        /*
         * CBS
         */
        public float $pCBS = 0,
        public float $pAliqEfetCBS = 0,
        public float $vCBS = 0,

        /*
         * Simples Nacional
         */
        public float $pIBSSN = 0,
        public float $vIBSSN = 0,

        public float $pCBSSN = 0,
        public float $vCBSSN = 0,

        /*
         * Total NF
         */
        public float $vTotNF = 0,
    ) {
    }
}