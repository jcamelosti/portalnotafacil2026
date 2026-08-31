<?php

namespace JCamelo\NfseNacionalLib\DTO;



class DPSDataDTO
{
    public function __construct(
        public string $ambiente,
        public string $dataEmissao,
        public int $serieDps,
        public int $numDps,

        public string $cnpjPrestador,
        public string $imPrestador,

        public string $cnpjTomador,
        public string $razaoTomador,
        public string $cMunTomador,
        public string $cepTomador,
        public string $logradouroTomador,
        public string $numeroTomador,
        public string $complementoTomador,
        public string $bairroTomador,
        public string $cPaisTomadorExterior,
        public string $cEndPostTomador,
        public string $xCidadeTomador,

 
        public string $localPrestacaoServico,

        public string $codigoMunicipio, //Local de Emissão
        public string $codigoTributacaoNacional,
        public string $codigoServico,
        public string $descricaoServico,
        public float $valorServico,
        public string $dataCompetencia,
        public string $nbs,


        public string $complemento,

        // 🔥 NOVOS CAMPOS
        public int $opSimpNac, // 1 ou 3
        public ?int $regApTribSN,
        public ?int $regEspTrib,

        public int $tribISSQN,
        public int $tpRetISSQN,

        public float $tribMunAliq, //trib->tribMun->pAliq
        public string $tribFedCst,
        public int $tpRetPisCofins,

        public float $vRetCP,
        public float $vRetIRRF,
        public float $vRetCSLL,

        public float $pTotTribSN,
        public string $cIndOp,
        public string $cstIbsCbs,
        public string $cClassTrib,
    ) {}
}

class DPSDataDTO2
{
    public function __construct(
        public string $cnpjPrestador,
        public string $imPrestador,

        public string $cnpjTomador,
        public string $razaoTomador,

        public string $codigoMunicipio,
        public string $codigoTributacaoNacional,
        public string $codigoServico,
        public string $descricaoServico,

        public float $valorServico,

        public string $dataCompetencia,

        /*
          Ambiente Emissão
          1 - Produção
          2 - Homologação      
        */
        public int $ambiente_emissao,

        /*
         * Regime tributário
         *
         * 1 = Não optante
         * 2 = MEI
         * 3 = Simples Nacional ME/EPP
         * 4 = Optante pendente
         */
        public int $opSimpNac,


        /**
         *Informação opcional.
			*Regime de Apuração Tributária pelo Simples Nacional.
			*Opção para que o contribuinte optante pelo Simples Nacional ME/EPP (opSimpNac = 3) possa indicar, ao emitir o documento fiscal, 
			*em qual regime de apuração os tributos federais e municipal estão inseridos, 
			*caso tenha ultrapassado algum sublimite ou limite definido para o Simples Nacional.
			*1 – Regime de apuração dos tributos federais e municipal pelo SN;
			*2 – Regime de apuração dos tributos federais pelo SN e o ISSQN pela NFS-e conforme respectiva legislação municipal do tributo;
			*3 – Regime de apuração dos tributos federais e municipal pela NFS-e conforme respectivas legilações federal e municipal de cada tributo.
        */
        //informação opcional mais deve enviar
        public int $regApTribSN = 3,


        //campo obrigatorio
        /**
        * Tipos de Regimes Especiais de Tributação Municipal:
		*	0 - Nenhum;
		*	1 - Ato Cooperado (Cooperativa);
		*	2 - Estimativa;
		*	3 - Microempresa Municipal;
		*	4 - Notário ou Registrador;
		*	5 - Profissional Autônomo;
		*	6 - Sociedade de Profissionais. 
         */
        public int $regEspTrib = 0,

        /*
         * Regime de apuração IBS/CBS
         *
         * 1 = IBS e CBS pelo Simples
         * 2 = CBS pelo Simples / IBS regular
         * 3 = IBS e CBS pelo regime regular
         */
        public ?int $regApIBSCBSSN = null,

        /*
         * Tributação IBS/CBS
         */
        public string $cst,
        public string $cClassTrib,
        public string $cIndOp,

        //Indicador da finalidade da emissão de NFS-e 
        public int $finNFSe = 0, //0 - NFS-e regular

        /*
         * Para operações de consumo pessoal
         */
        public int $indFinal = 0,

        /*
         * Destinatário
         */
        public int $indDest = 0,

        /*
         * Número da DPS
         */
        public int $numDps = 1,

        /*
         * Alíquotas
         *
         * Se não forem informadas,
         * serão determinadas pelo cálculo do ano/regime.
         */
        public ?float $pIBSUF = null,
        public ?float $pIBSMun = null,
        public ?float $pCBS = null,

        /*
         * Simples Nacional
         */
        public ?float $pIBSSN = null,
        public ?float $pCBSSN = null,

        /*
         * Deduções da base
         */
        public float $descIncond = 0,
        public float $vISSQN = 0,
        public float $vPIS = 0,
        public float $vCOFINS = 0,

        /*
         * Ajustes da base
         */
        public float $vCalcAjusteBCIBSCBS = 0,
        public float $vCalcAjusteBCLocImoveis = 0,
    ) {}
}