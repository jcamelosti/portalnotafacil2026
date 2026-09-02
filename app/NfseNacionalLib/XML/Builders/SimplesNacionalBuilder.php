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

        $tribMunAliq = number_format((float)$data->tribMunAliq, 2, '.', '');
		$vRetCSLL = number_format((float)$data->vRetCSLL, 2, '.', '');
		$vRetCP = number_format((float)$data->vRetCP, 2, '.', '');
		$vRetIRRF = number_format((float)$data->vRetIRRF, 2, '.', '');
		$valorServico = number_format((float)$data->valorServico, 2, '.', '');
		$pTotTribSN = number_format((float)$data->pTotTribSN, 2, '.', '');
		$cepTomador = preg_replace("/[^0-9]/", "", $data->cepTomador);
		
return '
<GerarNfseEnvio>
	<DPS versao="1.01">
		<infDPS Id="' . $dpsId . '">
			<tpAmb>' . $data->ambiente . '</tpAmb>
			<dhEmi>' . $data->dataEmissao . '</dhEmi>
			<verAplic>1.01</verAplic>
			<serie>' . $data->serieDps . '</serie>
			<nDPS>' . $data->numDps . '</nDPS>
			<dCompet>' . $data->dataCompetencia . '</dCompet>
			<tpEmit>1</tpEmit>
			<cLocEmi>' . $data->codigoMunicipio . '</cLocEmi>
			<prest>
				<CNPJ>' . $data->cnpjPrestador . '</CNPJ>
				<IM>' . $data->imPrestador . '</IM>
				<regTrib>
					<opSimpNac>' . $data->opSimpNac . '</opSimpNac>
					<regApTribSN>' . $data->regApTribSN . '</regApTribSN>
					<regEspTrib>' . $data->regEspTrib . '</regEspTrib>
				</regTrib>
			</prest>
			<toma>
				<CNPJ>' . $data->cnpjTomador . '</CNPJ>
				<xNome>' . $data->razaoTomador . '</xNome>
				<end>
					<endNac>
						<cMun>' . $data->cMunTomador . '</cMun>
						<CEP>' . $cepTomador . '</CEP>
					</endNac>
					<xLgr>' . $data->logradouroTomador . '</xLgr>
					<nro>' . $data->numeroTomador . '</nro>
					<xCpl>' . $data->complementoTomador . '</xCpl>
					<xBairro>' . $data->bairroTomador . '</xBairro>
				</end>
			</toma>
			<serv>
				<locPrest>
					<cLocPrestacao>' . $data->localPrestacaoServico . '</cLocPrestacao>
				</locPrest>
				<cServ>
					<cTribNac>' . $data->codigoTributacaoNacional . '</cTribNac>
					<cTribMun>' . $data->codigoServico . '</cTribMun>
					<xDescServ>' . $data->descricaoServico . '</xDescServ>
					<cNBS>' . $data->nbs . '</cNBS>
				</cServ>
				<infoCompl>
					<xInfComp>' . $data->complemento . '</xInfComp>
				</infoCompl>
			</serv>
			<valores>
				<vServPrest>
					<vServ>' . $valorServico . '</vServ>
				</vServPrest>
				<trib>
					<tribMun>
						<tribISSQN>' . $data->tribISSQN . '</tribISSQN>
						<tpRetISSQN>' . $data->tpRetISSQN . '</tpRetISSQN>
						<pAliq>' . $tribMunAliq . '</pAliq>
					</tribMun>
					<tribFed>
						<piscofins>
							<CST>' . $data->tribFedCst . '</CST>
							<tpRetPisCofins>' . $data->tpRetPisCofins . '</tpRetPisCofins>
						</piscofins>
						<vRetCP>' . $vRetCP . '</vRetCP>
						<vRetIRRF>' . $vRetIRRF . '</vRetIRRF>
						<vRetCSLL>' . $vRetCSLL . '</vRetCSLL>
					</tribFed>
					<totTrib>
						<pTotTribSN>' . $pTotTribSN . '</pTotTribSN>
					</totTrib>
				</trib>
			</valores>
			<IBSCBS>
				<finNFSe>0</finNFSe>
				<cIndOp>' . $data->cIndOp . '</cIndOp>
				<indDest>0</indDest>
				<valores>
					<trib>
						<gIBSCBS>
							<CST>' . $data->cstIbsCbs . '</CST>
							<cClassTrib>' . $data->cClassTrib . '</cClassTrib>
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

		$tribMunAliq = number_format((float)$data->tribMunAliq, 2, '.', '');
		$vRetCSLL = number_format((float)$data->vRetCSLL, 2, '.', '');
		$valorServico = number_format((float)$data->valorServico, 2, '.', '');

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
							<tpAmb>{$data->ambiente}</tpAmb>
							<dhEmi>{$data->dataEmissao}</dhEmi>
							<verAplic>1.01</verAplic>
							<serie>{$data->serieDps}</serie>
							<nDPS>{$data->numDps}</nDPS>
							<dCompet>2026-05-02</dCompet>
							<tpEmit>1</tpEmit>
							<cLocEmi>{$data->codigoMunicipio}</cLocEmi>
							<prest>
								<CNPJ>{$data->cnpjPrestador}</CNPJ>
								<IM>{$data->imPrestador}</IM>
								<regTrib>
									<opSimpNac>{$data->opSimpNac}</opSimpNac>
									<regApTribSN>{$data->regApTribSN}</regApTribSN>
									<regEspTrib>{$data->regEspTrib}</regEspTrib>
								</regTrib>
							</prest>
							<toma>
								<CNPJ>{$data->cnpjTomador}</CNPJ>
								<xNome>{$data->razaoTomador}</xNome>
								<end>
									<endNac>
										<cMun>{$data->cMunTomador}</cMun>
										<CEP>{$data->cepTomador}</CEP>
									</endNac>
									<xLgr>{$data->logradouroTomador}</xLgr>
									<nro>{$data->numeroTomador}</nro>
									<xCpl>{$data->complementoTomador}</xCpl>
									<xBairro>{$data->bairroTomador}</xBairro>
								</end>
							</toma>
							<serv>
								<locPrest>
									<cLocPrestacao>{$data->localPrestacaoServico}</cLocPrestacao>
								</locPrest>
								<cServ>
									<cTribNac>{$data->codigoTributacaoNacional}</cTribNac>
									<cTribMun>{$data->codigoServico}</cTribMun>
									<xDescServ>{$data->descricaoServico}</xDescServ>
									<cNBS>{$data->nbs}</cNBS>
								</cServ>
								<infoCompl>
									<xInfComp>{$data->complemento}</xInfComp>
								</infoCompl>
							</serv>
							<valores>
								<vServPrest>
									<vServ>{$valorServico}</vServ>
								</vServPrest>
								<trib>
									<tribMun>
										<tribISSQN>{$data->tribISSQN}</tribISSQN>
										<tpRetISSQN>{$data->tpRetISSQN}</tpRetISSQN>
										<pAliq>{$tribMunAliq}</pAliq>
									</tribMun>
									<tribFed>
										<piscofins>
											<CST>{$data->tribFedCst}</CST>
											<tpRetPisCofins>{$data->tpRetPisCofins}</tpRetPisCofins>
										</piscofins>
										<vRetCP>{$data->vRetCP}</vRetCP>
										<vRetIRRF>{$data->vRetIRRF}</vRetIRRF>
										<vRetCSLL>{$vRetCSLL}</vRetCSLL>
									</tribFed>
									<totTrib>
										<pTotTribSN>{$data->pTotTribSN}</pTotTribSN>
									</totTrib>
								</trib>
							</valores>
							<IBSCBS>
								<finNFSe>0</finNFSe>
								<cIndOp>{$data->cIndOp}</cIndOp>
								<indDest>0</indDest>
								<valores>
									<trib>
										<gIBSCBS>
											<CST>{$data->cstIbsCbs}</CST> 
											<cClassTrib>{$data->cClassTrib}</cClassTrib>
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