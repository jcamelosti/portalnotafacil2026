<?php

namespace JCamelo\NfseNacionalLib\DTO;

class DadosTributacao
{
    public function __construct(
        public float $vServ,

        public float $descIncond = 0,
        public float $vCalcAjusteBCIBSCBS = 0,
        public float $vCalcAjusteBCLocImoveis = 0,

        public float $vISSQN = 0,
        public float $vPIS = 0,
        public float $vCOFINS = 0,

        // Regime normal
        public float $pIBSUF = 0,
        public float $pIBSMun = 0,
        public float $pCBS = 0,

        // Reduções
        public float $pRedAliqUF = 0,
        public float $pRedAliqMun = 0,
        public float $pRedAliqCBS = 0,

        // Redutor geral
        public float $pRedutor = 0,

        // Diferimento
        public float $pDifUF = 0,
        public float $pDifMun = 0,
        public float $pDifCBS = 0,

        // Simples Nacional
        public float $pIBSSN = 0,
        public float $pCBSSN = 0,
    ) {}
}