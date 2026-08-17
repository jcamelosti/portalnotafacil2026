<?php
namespace JCamelo\NfseNacionalLib\XML\Builders;

use JCamelo\NfseNacionalLib\Contracts\DPSBuilderInterface;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class RegimeNormalBuilder implements DPSBuilderInterface
{
    /*public function build(DPSDataDTO $data): string
    {
        $id = "DPS{$data->codigoMunicipio}{$data->cnpjPrestador}0000000001";

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<DPS xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01">
    <infDPS Id="$id">
        <tpAmb>2</tpAmb>
        <dhEmi>{$data->dataCompetencia}T00:00:00-03:00</dhEmi>
        <verAplic>1.01</verAplic>
        <serie>1</serie>
        <nDPS>1</nDPS>
        <dCompet>{$data->dataCompetencia}</dCompet>
        <tpEmit>1</tpEmit>
        <cLocEmi>{$data->codigoMunicipio}</cLocEmi>

        <prest>
            <CNPJ>{$data->cnpjPrestador}</CNPJ>
            <IM>{$data->imPrestador}</IM>
            <regTrib>
                <opSimpNac>1</opSimpNac>
                <regEspTrib>0</regEspTrib>
            </regTrib>
        </prest>

        <toma>
            <CNPJ>{$data->cnpjTomador}</CNPJ>
            <xNome>{$data->razaoTomador}</xNome>
        </toma>

        <serv>
            <locPrest>
                <cLocPrestacao>{$data->codigoMunicipio}</cLocPrestacao>
            </locPrest>
            <cServ>
                <cTribNac>{$data->codigoTributacaoNacional}</cTribNac>
                <cTribMun>{$data->codigoServico}</cTribMun>
                <xDescServ>{$data->descricaoServico}</xDescServ>
            </cServ>
        </serv>

        <valores>
            <vServPrest>
                <vServ>{$data->valorServico}</vServ>
            </vServPrest>
        </valores>

    </infDPS>
</DPS>
XML;
    }*/
     protected function generateId(DPSDataDTO $data): string
    {
        $string = 'DPS';
        $string .= substr($data->codigoMunicipio, 0, 7); //Cód.Mun. (7) + //seria código do municipio do emitente
        $string .= (strlen($data->cnpjPrestador) === 14) ? 2 : 1; //Tipo de Inscrição Federal (1) +
        $string .= str_pad($data->cnpjPrestador, 14, 0, STR_PAD_LEFT); //Inscrição Federal (14 - CPF completar com 000 à esquerda) +
        $string .= str_pad('8', 5, 0, STR_PAD_LEFT); //Série DPS (5) +
        $string .= str_pad($data->numDps, 15, 0, STR_PAD_LEFT); //Série DPS (5) +*/

        return $string;
    }

    public function build(DPSDataDTO $data): string
    {
        $dpsId = $this->generateId($data);

        return <<<XML
        <GerarNfseEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
            <DPS versao="1.01">
                <infDPS Id="{$dpsId}">
                    <tpAmb>2</tpAmb>
                    <dhEmi>2026-05-02T10:19:01-03:00</dhEmi>
                    <verAplic>1.01</verAplic>
                    <serie>8</serie>
                    <nDPS>{$data->numDps}</nDPS>
                    <dCompet>2026-05-02</dCompet>
                    <tpEmit>1</tpEmit>
                    <cLocEmi>5002704</cLocEmi>
                    <prest>
                        <CNPJ>{$data->cnpjPrestador}</CNPJ>
                        <IM>{$data->imPrestador}</IM>
                        <fone>62991728787</fone>
                        <email>virlei79@gmail.com</email>
                        <regTrib>
                            <opSimpNac>1</opSimpNac>
                            <regEspTrib>0</regEspTrib>
                        </regTrib>
                    </prest>
                    <toma>
                        <CNPJ>{$data->cnpjTomador}</CNPJ>
                        <xNome>{$data->razaoTomador}</xNome>
                        <end>
                            <endNac>
                                <cMun>5201108</cMun>
                                <CEP>75064350</CEP>
                            </endNac>
                            <xLgr>Rua Carlinhos José Ribeiro</xLgr>
                            <nro>180</nro>
                            <xCpl>APT 402D</xCpl>
                            <xBairro>Vila Jaiara Setor Leste</xBairro>
                        </end>
                        <fone>6237027225</fone>
                        <email>contato@josuecamelo.com</email>
                    </toma>
                    <serv>
                        <locPrest>
                            <cLocPrestacao>5002704</cLocPrestacao>
                        </locPrest>
                        <cServ>
                            <cTribNac>010101</cTribNac>
                            <cTribMun>0000000004</cTribMun>
                            <xDescServ>Teste 1</xDescServ>
                            <cNBS>115021000</cNBS>
                        </cServ>
                        <infoCompl>
                            <xInfComp>Teste - Texto Informativo</xInfComp>
                        </infoCompl>
                    </serv>
                    <valores>
                        <vServPrest>
                            <vServ>1.00</vServ>
                        </vServPrest>
                        <trib>
                            <tribMun>
                                <tribISSQN>1</tribISSQN>
                                <tpRetISSQN>2</tpRetISSQN>
                            </tribMun>
                            <tribFed>
                                <piscofins>
                                    <CST>01</CST>
                                    <vBCPisCofins>7801.08</vBCPisCofins>
                                    <pAliqPis>0.65</pAliqPis>
                                    <pAliqCofins>3.00</pAliqCofins>
                                    <vPis>50.71</vPis>
                                    <vCofins>234.03</vCofins>
                                    <tpRetPisCofins>1</tpRetPisCofins>
                                </piscofins>
                                <vRetIRRF>117.02</vRetIRRF>
                                <vRetCSLL>78.01</vRetCSLL>
                            </tribFed>
                            <totTrib>
                                <vTotTrib>
                                    <vTotTribFed>1049.25</vTotTribFed>
                                    <vTotTribEst>0.00</vTotTribEst>
                                    <vTotTribMun>156.02</vTotTribMun>
                                </vTotTrib>
                            </totTrib>
                        </trib>
                    </valores>
                    <IBSCBS>
                        <finNFSe>0</finNFSe>
                        <indFinal>0</indFinal>
                        <cIndOp>050103</cIndOp>
                        <indDest>0</indDest>
                        <valores>
                            <trib>
                                <gIBSCBS>
                                    <CST>000</CST>
                                    <cClassTrib>000001</cClassTrib>
                                </gIBSCBS>
                            </trib>
                        </valores>
                    </IBSCBS>
                </infDPS>
            </DPS>
        </GerarNfseEnvio>
        XML;
    }

    public function buildRecepcionarLoteDpsSincrono(DPSDataDTO $data): string
	{
		$dpsId = $this->generateId($data);
		
		return <<<XML
        <EnviarLoteDpsSincronoEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
            <LoteDps Id="L{$dpsId}" versao="1.01">
                <NumeroLote>1</NumeroLote>

                <Prestador>
                    <CNPJ>{$data->cnpjPrestador}</CNPJ>
                    <IM>{$data->imPrestador}</IM>
                </Prestador>

                <QuantidadeDps>1</QuantidadeDps>

                <ListaDps>
                    <DPS versao="1.01">
                        <infDPS Id="{$dpsId}">

                            <tpAmb>2</tpAmb>
                            <dhEmi>2026-05-02T10:19:01-03:00</dhEmi>
                            <verAplic>1.01</verAplic>
                            <serie>8</serie>
                            <nDPS>{$data->numDps}</nDPS>
                            <dCompet>2026-05-02</dCompet>
                            <tpEmit>1</tpEmit>
                            <cLocEmi>5002704</cLocEmi>

                            <prest>
                                <CNPJ>{$data->cnpjPrestador}</CNPJ>
                                <IM>{$data->imPrestador}</IM>
                                <fone>62991728787</fone>
                                <email>virlei79@gmail.com</email>

                                <regTrib>
                                    <opSimpNac>1</opSimpNac>
                                    <regEspTrib>0</regEspTrib>
                                </regTrib>
                            </prest>

                            <toma>
                                <CNPJ>{$data->cnpjTomador}</CNPJ>
                                <xNome>{$data->razaoTomador}</xNome>

                                <end>
                                    <endNac>
                                        <cMun>5201108</cMun>
                                        <CEP>75064350</CEP>
                                    </endNac>

                                    <xLgr>Rua Carlinhos José Ribeiro</xLgr>
                                    <nro>180</nro>
                                    <xCpl>APT 402D</xCpl>
                                    <xBairro>Vila Jaiara Setor Leste</xBairro>
                                </end>

                                <fone>6237027225</fone>
                                <email>contato@josuecamelo.com</email>
                            </toma>

                            <serv>
                                <locPrest>
                                    <cLocPrestacao>5002704</cLocPrestacao>
                                </locPrest>

                                <cServ>
                                    <cTribNac>010101</cTribNac>
                                    <cTribMun>0000000004</cTribMun>
                                    <xDescServ>Teste 1</xDescServ>
                                    <cNBS>115021000</cNBS>
                                </cServ>

                                <infoCompl>
                                    <xInfComp>Teste - Texto Informativo</xInfComp>
                                </infoCompl>
                            </serv>

                            <valores>
                                <vServPrest>
                                    <vServ>1.00</vServ>
                                </vServPrest>

                                <trib>
                                    <tribMun>
                                        <tribISSQN>1</tribISSQN>
                                        <tpRetISSQN>2</tpRetISSQN>
                                    </tribMun>

                                    <tribFed>
                                        <piscofins>
                                            <CST>01</CST>
                                            <vBCPisCofins>7801.08</vBCPisCofins>
                                            <pAliqPis>0.65</pAliqPis>
                                            <pAliqCofins>3.00</pAliqCofins>
                                            <vPis>50.71</vPis>
                                            <vCofins>234.03</vCofins>
                                            <tpRetPisCofins>1</tpRetPisCofins>
                                        </piscofins>

                                        <vRetIRRF>117.02</vRetIRRF>
                                        <vRetCSLL>78.01</vRetCSLL>
                                    </tribFed>

                                    <totTrib>
                                        <vTotTrib>
                                            <vTotTribFed>1049.25</vTotTribFed>
                                            <vTotTribEst>0.00</vTotTribEst>
                                            <vTotTribMun>156.02</vTotTribMun>
                                        </vTotTrib>
                                    </totTrib>
                                </trib>
                            </valores>

                            <IBSCBS>
                                <finNFSe>0</finNFSe>
                                <indFinal>0</indFinal>
                                <cIndOp>050103</cIndOp>
                                <indDest>0</indDest>

                                <valores>
                                    <trib>
                                        <gIBSCBS>
                                            <CST>000</CST>
                                            <cClassTrib>000001</cClassTrib>
                                        </gIBSCBS>
                                    </trib>
                                </valores>
                            </IBSCBS>

                        </infDPS>
                    </DPS>
                </ListaDps>

            </LoteDps>
        </EnviarLoteDpsSincronoEnvio>
        XML;
	}
}