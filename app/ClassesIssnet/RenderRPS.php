<?php

namespace NFePHP\NFSe\Models\Issnet;

use DateTimeZone;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Issnet\Rps;

class RenderRPS
{
    /**
     * @var DOMImproved
     */
    protected static $dom;
    /**
     * @var Certificate
     */
    protected static $certificate;
    /**
     * @var int
     */
    protected static $algorithm;
    /**
     * @var \DateTimeZone
     */
    protected static $timezone;

    public static function toXml($data)
    {
        $xml = '';
        if (is_object($data)) {
            return self::render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= self::render($rps);
            }
        }
        return $xml;
    }
    
    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    private static function render(RpsClass $rps)
    {
        self::$dom = new Dom('1.0', 'utf-8');
        $root = self::$dom->createElement('Rps');
        $infRPS = self::$dom->createElement('InfDeclaracaoPrestacaoServico');//Rps

        //Criando Elemento Rps de Dentro do InfDeclaracaoPrestacaoServico
        $rpsDetalhe = self::$dom->createElement('Rps');

        self::$dom->appChild($infRPS, $rpsDetalhe, 'Adicionando tag Rps em InfDeclaracaoPrestacaoServico');
        self::$dom->appendChild($root);

        //Rps Identificação
        $rpsIdentificacao = self::$dom->createElement('IdentificacaoRps');
        self::$dom->appChild($rpsDetalhe, $rpsIdentificacao, 'Adicionando tag IdentificacaoRps em Rps');
        self::$dom->appendChild($root);

        self::$dom->addChild(
            $rpsIdentificacao,
            'Numero',
            $rps->infNumero,
            true,
            "Numero do RPS",
            true
        );
        self::$dom->addChild(
            $rpsIdentificacao,
            'Serie',
            $rps->infSerie,
            true,
            "Serie do RPS",
            true
        );
        self::$dom->addChild(
            $rpsIdentificacao,
            'Tipo',
            $rps->infTipo,
            true,
            "Tipo do RPS",
            true
        );

        //Data de Emissão dentro do RPS
        $h = "3";
        $hm = $h * 60;
        $ms = $hm * 60;
        $gmdata = gmdate("Y-m-d", time() - ($ms));
        $gmhora = gmdate("H:i:s", time() - ($ms));
        $rps->infDataEmissao = $gmdata;//. 'T' . $gmhora;
         //final josue
     
        self::$dom->addChild(
            $rpsDetalhe,
            'DataEmissao',
            $rps->infDataEmissao,
            true,
            'Data de Emissão do RPS',
            false
        );

        //Status
        self::$dom->addChild(
            $rpsDetalhe,
            'Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        //Competencia
        self::$dom->addChild(
            $infRPS,
            'Competencia',
            $rps->infDataCompetencia->format('Y-m-d'),
            true,
            'Competencia',
            false
        );

        //Serviço
        $servicoArea = self::$dom->createElement('Servico');
        self::$dom->appChild($infRPS, $servicoArea, 'Adicionando tag Servico em InfDeclaracaoPrestacaoServico');
        self::$dom->appendChild($root);

        //Valores
        $area = self::$dom->createElement('Valores');
        self::$dom->appChild($servicoArea, $area, 'Adicionando tag Valores em Servicos');
        self::$dom->appendChild($root);

        self::$dom->addChild(
            $area,
            'ValorServicos',
            $rps->infValorServicos,
            true,
            'ValorServicos',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorDeducoes',
            $rps->infValorDeducoes,
            false,
            'ValorDeducoes',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorPis',
            $rps->infValorPis,
            false,
            'ValorPis',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorCofins',
            $rps->infValorCofins,
            false,
            'ValorCofins',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorInss',
            $rps->infValorInss,
            false,
            'ValorInss',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorIr',
            $rps->infValorIr,
            false,
            'ValorIr',
            false
        );
        self::$dom->addChild(
            $area,
            'ValorCsll',
            $rps->infValorCsll,
            false,
            'ValorCsll',
            false
        );
        self::$dom->addChild(
            $area,
            'OutrasRetencoes',
            $rps->infOutrasRetencoes,
            false,
            'OutrasRetencoes',
            false
        );
        //voltar se der erro
        /*self::$dom->addChild(
            $area,
            'ValTotTributos',
            $rps->valorTotalTributos, //INFORMAR VALOR TOTAL DOS TRIBUTOS
            false,
            'ValTotTributos',
            false
        );*/

        self::$dom->addChild(
            $area,
            'ValorIss',
            $rps->infValorIss,
            false,
            'ValorIss',
            false
        );

        if(!is_null($rps->infAliquota)){
            self::$dom->addChild(
                $area,
                'Aliquota',
                number_format($rps->infAliquota, 2, '.', ''),
                false,
                'Aliquota',
                false
            );
        }
        self::$dom->addChild(
            $area,
            'DescontoIncondicionado',
            $rps->infDescontoIncondicionado,
            false,
            'DescontoIncondicionado',
            false
        );
        self::$dom->addChild(
            $area,
            'DescontoCondicionado',
            $rps->infDescontoCondicionado,
            false,
            'DescontoCondicionado',
            false
        );
        
        //ISSRETIDO?
        self::$dom->addChild(
            $servicoArea,
            'IssRetido',
            $rps->infIssRetido,
            true,
            'IssRetido',
            false
        );
        /*Informado somente se IssRetido igual a "1 – Sim".
        A opção "2 – Intermediário" somente poderá ser selecionada se "CpfCnpjIntermediario" informado.
        1 – Tomador;
        2 – Intermediário. */
        if($rps->infIssRetido == 1){
            self::$dom->addChild(
                $servicoArea,
                'ResponsavelRetencao',
                1,
                true,
                'ResponsavelRetencao',
                false
            );
        }

        //ITEM LISTA SERVIÇO
        self::$dom->addChild(
            $servicoArea,
            'ItemListaServico',
            $rps->infItemListaServico,
            true,
            'ItemListaServico',
            false
        );
        self::$dom->addChild(
            $servicoArea,
            'CodigoCnae',
            $rps->infCodigoCnae,
            true,
            'CodigoCnae',
            false
        );
        self::$dom->addChild(
            $servicoArea,
            'CodigoTributacaoMunicipio',
            $rps->infCodigoTributacaoMunicipio,
            true,
            'CodigoTributacaoMunicipio',
            false
        );

        if(!empty($rps->infCodigoNbs)){
            self::$dom->addChild(
                $servicoArea,
                'CodigoNbs',
                $rps->infCodigoNbs,
                true,
                'CodigoNbs',
                false
            );
        }
        
        self::$dom->addChild(
            $servicoArea,
            'Discriminacao',
            $rps->infDiscriminacao,
            true,
            'Discriminacao',
            false
        );

        //Codigo Municipio
        self::$dom->addChild(
            $servicoArea,
            'CodigoMunicipio',
            $rps->infMunicipioPrestacaoServico,
            true,
            'CodigoMunicipio',
            false
        );
        
        /*if($rps->infTomadorEndereco['cmun'] == '99999'){
            //Codigo do PAIS IBGE -- só informar se exibibilidade do iss for 4 - exportação
            self::$dom->addChild(
                $servicoArea,
                'CodigoPais',
                99999,
                true,
                'CodigoPais',
                false
            );
        }*/

        /*Exigibilidades possíveis
						1 – Exigível;
						2 – Não incidência;
						3 – Isenção;
						4 – Exportação;
						5 – Imunidade;
						6 – Exigibilidade Suspensa por Decisão Judicial;
						7 – Exigibilidade Suspensa por Processo Administrativo.
						<ExigibilidadeISS>?</ExigibilidadeISS>*/
        //quando for exportação
        //if($rps->infTomadorEndereco['cmun'] != '99999'){
            self::$dom->addChild(
                $servicoArea,
                'ExigibilidadeISS',
                1,
                true,
                'ExigibilidadeISS',
                false
            );
        /*else{//exportação para exterior
            self::$dom->addChild(
                $servicoArea,
                'ExigibilidadeISS',
                4,
                true,
                'ExigibilidadeISS',
                false
            );
        }*/

        self::$dom->addChild(
            $servicoArea,
            'IdentifNaoExigibilidade',
            1,//VER VALOR CORRETO
            true,
            'IdentifNaoExigibilidade',
            false
        );
        
        //este campo deve ser igual ao municipio que a empresa emissora é cadastrada
        //só informar se a incidencia for fora do municipio - tributação fora do municipio
        self::$dom->addChild(
            $servicoArea,
            'MunicipioIncidencia',
            $rps->infMunicipioIncidencia,//alterado aqui
            true,
            'MunicipioIncidencia',
            false
        );
        /*self::$dom->addChild(
            $servicoArea,
            'NumeroProcesso',
            1,//descobrir de onde vem
            true,
            'NumeroProcesso',
            false
        );*/

        //Prestador
        $prestadorArea = self::$dom->createElement('Prestador');
        self::$dom->appChild($infRPS, $prestadorArea, 'Adicionando tag Prestador em Rps');
        self::$dom->appendChild($root);

        $area = self::$dom->createElement('CpfCnpj');
        self::$dom->appChild($prestadorArea, $area, 'Adicionando tag CpfCnpj em Prestador');
        self::$dom->appendChild($root);

        if ($rps->infPrestador['tipo'] == 2) {
            self::$dom->addChild(
                $area,
                'Cnpj',
                $rps->infPrestador['cnpjcpf'],
                true,
                'Prestador CNPJ',
                false
            );
        } else {
            self::$dom->addChild(
                $area,
                'Cpf',
                $rps->infPrestador['cnpjcpf'],
                true,
                'Prestador CPF',
                false
            );
        }

        self::$dom->addChild(
            $prestadorArea,
            'InscricaoMunicipal',
            $rps->infPrestador['im'],
            true,
            'InscricaoMunicipal',
            false
        );


        //TomadorServico
        $tomadorArea = self::$dom->createElement('TomadorServico');
        self::$dom->appChild($infRPS, $tomadorArea, 'Adicionando tag TomadorServico em Rps');
        self::$dom->appendChild($root);

        if($rps->infTomadorEndereco['cmun'] != '99999'){
            $identificadorArea = self::$dom->createElement('IdentificacaoTomador');
            self::$dom->appChild($tomadorArea, $identificadorArea, 'Adicionando tag CpfCnpj em Prestador');
            self::$dom->appendChild($root);
            
            $area = self::$dom->createElement('CpfCnpj');
            self::$dom->appChild($identificadorArea, $area, 'Adicionando tag CpfCnpj em Prestador');
            self::$dom->appendChild($root);

            if ($rps->infTomador['tipo'] == RpsClass::CNPJ) {
                self::$dom->addChild(
                    $area,
                    'Cnpj',
                    $rps->infTomador['cnpjcpf'],
                    true,
                    'Prestador CNPJ',
                    false
                );
            } else {
                self::$dom->addChild(
                    $area,
                    'Cpf',
                    $rps->infTomador['cnpjcpf'],
                    true,
                    'Prestador CPF',
                    false
                );
            }
            if(!empty(trim($rps->infTomador['im']))){
                self::$dom->addChild(
                    $identificadorArea,
                    'InscricaoMunicipal',
                    $rps->infTomador['im'],
                    true,
                    'InscricaoMunicipal',
                    false
                );
            }
        }

        //Este elemento só deverá ser preenchido para tomadores não residentes no Brasil
        if($rps->infTomadorEndereco['cmun'] == '99999'){
            self::$dom->addChild(
                $tomadorArea,
                'NifTomador',
                $rps->nif,
                true,
                'NifTomador',
                false
            );
        }

        self::$dom->addChild(
            $tomadorArea,
            'RazaoSocial',
            $rps->infTomador['razao'],
            true,
            'RazaoSocial',
            false
        );


        //<!-- Informar apenas uma das Tags. Ou tag Endereco ou Tag EnderecoExterior. -->
        //Endereço Brasil
        if($rps->infTomadorEndereco['cmun'] != '99999'){
            $enderecoArea = self::$dom->createElement('Endereco');
            self::$dom->appChild($tomadorArea, $enderecoArea, 'Adicionando tag Endereco em TomadorServico');
            self::$dom->appendChild($root);

            self::$dom->addChild(
                $enderecoArea,
                'Endereco',
                $rps->infTomadorEndereco['end'],
                true,
                'Endereco',
                false
            );
            
            if(!empty($rps->infTomadorEndereco['numero'])){
                self::$dom->addChild(
                    $enderecoArea,
                    'Numero',
                    $rps->infTomadorEndereco['numero'],
                    true,
                    'Numero',
                    false
                );
            }

            $complemento = trim($rps->infTomadorEndereco['complemento'] ?? '');
            if (strlen($complemento) > 1) {
                self::$dom->addChild(
                    $enderecoArea,
                    'Complemento',
                    $complemento,
                    true,
                    'Complemento',
                    false
                );
            }


            self::$dom->addChild(
                $enderecoArea,
                'Bairro',
                $rps->infTomadorEndereco['bairro'],
                true,
                'Bairro',
                false
            );

            self::$dom->addChild(
                $enderecoArea,
                'CodigoMunicipio',
                $rps->infTomadorEndereco['cmun'],
                true,
                'CodigoMunicipio',
                false
            );
            self::$dom->addChild(
                $enderecoArea,
                'Uf',
                $rps->infTomadorEndereco['uf'],
                true,
                'Uf',
                false
            );
            self::$dom->addChild(
                $enderecoArea,
                'Cep',
                $rps->infTomadorEndereco['cep'],
                true,
                'Cep',
                false
            );
        }else{
            //Endereço Exterior - Campo se o Codigo Pais for Diferente Brasil
            $enderecoExteriorArea = self::$dom->createElement('EnderecoExterior');
            self::$dom->appChild($tomadorArea, $enderecoExteriorArea, 'Adicionando tag EnderecoExterior em TomadorServico');
            self::$dom->appendChild($root);

            self::$dom->addChild(
                $enderecoExteriorArea,
                'CodigoPais',
                $rps->codPaisExterior,//1058 = Brasil - 02496(EUA)
                true,
                'CodigoPais',
                false
            );

            self::$dom->addChild(
                $enderecoExteriorArea,
                'EnderecoCompletoExterior',
                $rps->infTomadorEndereco['end'],
                true,
                'EnderecoCompletoExterior',
                false
            );
        }

        $telefone = trim($rps->infTomador['tel'] ?? '');
        $email    = trim($rps->infTomador['email'] ?? '');
        
        //Contato
        if ( (strlen($telefone) > 1) || (strlen($email) > 1) ) {
            $contatoArea = self::$dom->createElement('Contato');
            self::$dom->appChild($tomadorArea, $contatoArea, 'Adicionando tag Contato em TomadorServico');
            self::$dom->appendChild($root);

            if ( strlen($telefone) > 1){
                self::$dom->addChild(
                    $contatoArea,
                    'Telefone',
                    $rps->infTomador['tel'],
                    false,
                    'Telefone Tomador',
                    false
                );
            }

            if ( strlen($email) > 1){
                self::$dom->addChild(
                    $contatoArea,
                    'Email',
                    $rps->infTomador['email'],
                    false,
                    'Email Tomador',
                    false
                );
            }
        }

        //ConstrucaoCivil
        /*$construcaoCivilArea = self::$dom->createElement('ConstrucaoCivil');
        self::$dom->appChild($rpsDetalhe, $construcaoCivilArea, 'Adicionando tag ConstrucaoCivil em Rps');
        self::$dom->appendChild($root);*/

        //RegimeEspecialTributacao
        self::$dom->addChild(
            $infRPS,
            'RegimeEspecialTributacao',
            $rps->infRegimeEspecialTributacao,
            true,
            'RegimeEspecialTributacao',
            false
        );

        //OptanteSimplesNacional
        /*if(in_array($rps->infRegimeEspecialTributacao, [5,6])){
            self::$dom->addChild(
                $infRPS,
                'OptanteSimplesNacional',
                1,
                true,
                'OptanteSimplesNacional',
                false
            );
        }else{*/
            self::$dom->addChild(
                $infRPS,
                'OptanteSimplesNacional',
                $rps->infOptanteSimplesNacional,
                true,
                'OptanteSimplesNacional',
                false
            );
        //}

        //IncentivadorCultural - NÃO EXISTE
        /*self::$dom->addChild(
            $infRPS,
            'IncentivadorCultural',
            $rps->infIncentivadorCultural,
            true,
            'IncentivadorCultural',
            false
        );*/

        //IncentivoFiscal
        self::$dom->addChild(
            $infRPS,
            'IncentivoFiscal',
            $rps->infIncentivadorCultural,//NÃO - E  1 => SIM
            true,
            'IncentivoFiscal',
            false
        );

        self::$dom->addChild(
            $infRPS,
            'InformacoesComplementares',
            $rps->informacoesComplementares,
            true,
            'InformacoesComplementares',
            false
        );
       
        //Adicionando no Root <RPS>
        self::$dom->appChild($root, $infRPS, 'Adicionando tag infRPS em RPS');
        self::$dom->appendChild($root);

        /*$xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());*/
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());   
        
        return $xml;
    }
}
