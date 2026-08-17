<?php

namespace JCamelo\NfseNacionalLib\XML\Builders;

use JCamelo\NfseNacionalLib\Contracts\DPSBuilderInterface;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;

class SimplesNacionalBuilder implements DPSBuilderInterface
{
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
        return '
<GerarNfseEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
	<DPS versao="1.01">
		<infDPS Id="' . $dpsId . '">
			<tpAmb>2</tpAmb>
			<dhEmi>2026-05-02T10:19:01-03:00</dhEmi>
			<verAplic>1.01</verAplic>
			<serie>8</serie>
			<nDPS>' . $data->numDps . '</nDPS>
			<dCompet>2026-05-02</dCompet>
			<tpEmit>1</tpEmit>
			<cLocEmi>5002704</cLocEmi>
			<prest>
				<CNPJ>22645177000188</CNPJ>
				<IM>4048539</IM>
				<fone>62991728787</fone>
				<email>virlei79@gmail.com</email>
				<regTrib>
					<opSimpNac>3</opSimpNac>
					<regApTribSN>1</regApTribSN>
					<regEspTrib>0</regEspTrib>
				</regTrib>
			</prest>
			<toma>
				<CNPJ>24685881000190</CNPJ>
				<IM>79649</IM>
				<xNome>Josue Camelo dos Santos Ferreira 01582713197</xNome>
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
						<tpRetISSQN>1</tpRetISSQN>
						<pAliq>2.50</pAliq>
					</tribMun>
					<tribFed>
						<piscofins>
							<CST>00</CST>
							<tpRetPisCofins>0</tpRetPisCofins>
						</piscofins>
						<vRetCP>0.12</vRetCP>
						<vRetIRRF>0.01</vRetIRRF>
					</tribFed>
					<totTrib>
						<pTotTribSN>5.00</pTotTribSN>
					</totTrib>
				</trib>
			</valores>
			<IBSCBS>
				<finNFSe>0</finNFSe>
				<cIndOp>100301</cIndOp>
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
</GerarNfseEnvio>';
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
								<CNPJ>22645177000188</CNPJ>
								<IM>4048539</IM>
								<fone>62991728787</fone>
								<email>virlei79@gmail.com</email>
								<regTrib>
									<opSimpNac>3</opSimpNac>
									<regApTribSN>1</regApTribSN>
									<regEspTrib>0</regEspTrib>
								</regTrib>
							</prest>
							<toma>
								<CNPJ>24685881000190</CNPJ>
								<IM>79649</IM>
								<xNome>Josue Camelo dos Santos Ferreira 01582713197</xNome>
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
										<tpRetISSQN>1</tpRetISSQN>
										<pAliq>2.50</pAliq>
									</tribMun>
									<tribFed>
										<piscofins>
											<CST>00</CST>
											<tpRetPisCofins>0</tpRetPisCofins>
										</piscofins>
										<vRetCP>0.12</vRetCP>
										<vRetIRRF>0.01</vRetIRRF>
									</tribFed>
									<totTrib>
										<pTotTribSN>5.00</pTotTribSN>
									</totTrib>
								</trib>
							</valores>
							<IBSCBS>
								<finNFSe>0</finNFSe>
								<cIndOp>100301</cIndOp>
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