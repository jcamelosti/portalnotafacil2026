<?php

namespace App\Http\Controllers\Api;

use App\Business\NotasBO;
use Illuminate\Http\Request;
use App\Traits\IssnetTrait;
use App\Models\Empresa;
use App\Models\License;
use App\Models\NotaEmitida;
use App\Models\Tomador;
use Illuminate\Support\Facades\Validator;
use App\Utilitarios\Utilitarios;
use Illuminate\Support\Facades\DB;
use NFePHP\NFSe\Models\Issnet\RpsClass;
use Illuminate\Support\Facades\Log;
use stdClass;

class NotaController extends BaseController
{
    use IssnetTrait;
    
    private $notaBO;
    private $empresaModel;
    private $tomadorModel;
    private $nfseModel;

    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, NotaEmitida $nfseModel){
        $this->notaBO = NotasBO::newInstance();
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
        $this->nfseModel = $nfseModel;
    }

    public function index(Request $request)
    {
    }

    public function store(Request $request){
        $dados = $request->all();
        $dados = $this->notaBO->tratarDadosApi($dados);
        
        $mensagens = [
            'prestador.cpf_cnpj.required' => 'O Campo :attribute deve ser informado',
            'prestador.cpf_cnpj.min' => 'O Campo :attribute deve ser informado com 14 digitos.',
            'prestador.cpf_cnpj.max' => 'O Campo :attribute deve ser informado com 14 digitos.',

            'tomador.endereco.array' => 'O Campo :attribute deve ser um Array',
            'tomador.endereco.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.logradouro.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.numero.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.complemento.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.bairro.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.cidade_id.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.uf_sigla.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.cep.required' => 'O Campo :attribute deve ser informado e preenchido',
            'tomador.endereco.cep.size' => 'O Campo :attribute deve ser informado com no máximo 8 digitos',
            'tomador.endereco.uf_sigla.min' => 'O Campo :attribute deve ser informado com 2 digitos. Ex: MG',
            'tomador.endereco.uf_sigla.max' => 'O Campo :attribute deve ser informado com 2 digitos. Ex: MG',
            'tomador.endereco.cidade_id.min' => 'O Campo :attribute deve ser informado com 7 digitos. Ex: 3162500',
            'tomador.endereco.cidade_id.max' => 'O Campo :attribute deve ser informado com 7 digitos. Ex: 3162500',
            
            'tomador.cpf_cnpj.required' => 'O Campo :attribute deve ser informado',
            'tomador.cpf_cnpj.min' => 'O Campo :attribute deve ser informado com 14 digitos.',
            'tomador.cpf_cnpj.max' => 'O Campo :attribute deve ser informado com 14 digitos.',

            'dados_nota.iss_retido.required' => 'O Campo :attribute deve ser informado. 1 => Iss Retido, 2 => Sem Retenção',
            'dados_nota.iss_retido.in' => 'O Campo :attribute deve ser informado. 1 => Iss Retido, 2 => Sem Retenção',
            'dados_nota.municipio_prestacao_servico.required' => 'O Campo :attribute deve ser informado com 7 digitos. Ex: 3162500',
            'dados_nota.municipio_prestacao_servico..min' => 'O Campo :attribute deve ser informado com 7 digitos. Ex: 3162500',
            'dados_nota.municipio_prestacao_servico..max' => 'O Campo :attribute deve ser informado com 7 digitos. Ex: 3162500',
        ];
        $validator = Validator::make($dados, [
            'prestador' => 'array|required',
            'prestador.cpf_cnpj' => 'required|min:14|max:14',
            'prestador.inscricao_municipal' => 'required|numeric',

            'tomador.endereco' => 'array',
            'tomador.endereco.logradouro' => 'required',
            'tomador.endereco.numero' => 'numeric:min:1|nullable',
            'tomador.endereco.complemento' => 'required',
            'tomador.endereco.bairro' => 'required',
            'tomador.endereco.cidade_id' => 'required|min:7|max:7',
            'tomador.endereco.uf_sigla' => 'required:min:2|max:2',
            'tomador.endereco.cep' => 'required|size:8',

            'tomador.cpf_cnpj' => 'required|min:11|max:14',
            'tomador.inscricao_municipal' => 'nullable|numeric',
            'tomador.razao_social' => 'required',
            'tomador.telefone' => 'required|min:10|max:11',
            'tomador.email' => 'nullable|email',

            'dados_nota' => 'array',
            //'dados_nota.numero_doc' =>'required|min:15|max:15',
            'dados_nota.iss_retido' => 'required|in:1,2',
            'dados_nota.municipio_prestacao_servico' => 'required|min:7|max:7',
            'dados_nota.descricao_servico' =>  "required|max:2000|min:5",
            'dados_nota.aliquota' => 'required|numeric',
            'dados_nota.valor_total' => 'required|numeric',
            'dados_nota.valor_deducao_base_calculo' => 'required|numeric',
            'dados_nota.valor_pis' => 'required|numeric',
            'dados_nota.valor_cofins' => 'required|numeric',
            'dados_nota.valor_csll' => 'required|numeric',
            'dados_nota.valor_irrf' => 'required|numeric',
            'dados_nota.valor_outros' => 'required|numeric',
            'dados_nota.valor_outras_retencoes' => 'required|numeric',
            'dados_nota.valor_desconto_condicionado' => 'required|numeric',
            'dados_nota.valor_desconto_incondicionado' => 'required|numeric',
        ], $mensagens);
 
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => [
                   'errors' => json_decode($validator->errors()->toJson())
                ],
                'message' => 'Erro de Validação de Dados'
            ]);
        }

        //validar se tem plano api e se tem vigencia valida
        $empresaSessao = $this->empresaModel
            ->where('cpf_cnpj', $dados['prestador']['cpf_cnpj'])
            ->where('inscricao_municipal', $dados['prestador']['inscricao_municipal'])
            ->first();

        if(is_null($empresaSessao)){
            return response()->json([
                'error' => 1,
                'data' => null,
                'message' => 'Empresa Nâo Cadastrada'
            ]);
        }

        $license = License::whereDate('validate', '>=', DB::raw('CURDATE()'))
            ->where('empresa_id', $empresaSessao->id)->first();

        $temLicencaValida = true;
        $hj = date('Y-m-d');

        if(is_null($license)){
            $temLicencaValida = false;
        }else{
            if ($hj > $license->validate) {
                $temLicencaValida = false;
            }
        }

        if(!$temLicencaValida){
            return response()->json([
                'error' => 1,
                'data' => null,
                'message' => 'A Empresa não possui licença ativa.'
            ]);
        }

        if($empresaSessao->plano_id != 3){
            return response()->json([
                'error' => 1,
                'data' => null,
                'message' => 'A Empresa não possui o recurso API ativado.'
            ]);
        }

        $retorno = $this->emitir($dados);
        
        return response()->json($retorno);
    }

    protected function emitir($dados){
        $retorno = [];
        
        $rps = $this->gerarRPS($dados); 
        $xml = $this->gerarXmlAbrasf204Api([$rps]);

        $empresaSessao = $this->empresaModel
            ->where('cpf_cnpj', $dados['prestador']['cpf_cnpj'])
            ->where('inscricao_municipal', $dados['prestador']['inscricao_municipal'])
            ->first();
        
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml); 

        //Assinando Novamente o RPS
        $xml = $this->assinarRpsRepetidamenteApi($xml, 'EnviarLoteRpsSincronoEnvio', $empresaSessao->cpf_cnpj); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml);
        
        $xml = str_replace("&#xD;", "\n\r", $xml);//quebra de linha na descrição da nota que estava errado.

        //Log::info($xml);
        
        //TRANSMITINDO O RPS
        $resposta = $this->sendRequest($xml, 'RecepcionarLoteRpsSincrono', $empresaSessao);
        $array = json_decode(json_encode($resposta), TRUE); 

        //Log::info($array);

        if(isset($array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['ListaMensagemRetorno'])){
            $erro = $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['ListaMensagemRetorno']['MensagemRetorno'];
            $mensagem = '';

            if( is_array($erro) && isset($erro['Codigo']) && isset($erro['Mensagem'])){
                $mensagem = "Erro: #" . $erro['Codigo']. ' - ' . $erro['Mensagem'];
                $mensagem .= ' Solução: Para Corrigir o Erro ' . $erro['Correcao'];
            }else{
                foreach($erro as $err){
                    $mensagem .= "Erro: #" . $err['Codigo']. ' - ' . $err['Mensagem'];
                    $mensagem .= ' Solução: Para Corrigir o Erro ' . $err['Correcao'];
                    $mensagem .= ' / ';
                }
            }

            $retorno = [
               'error' => 1,
               'data' => null,
               'message' => $mensagem
            ];
        }else{
            $dados = $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta'];
            $declaracao_servico_prestado = $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['DeclaracaoPrestacaoServico'];

            //NUM NOTA
            $numero_nota = $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['Numero'];
            $numero_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Numero'];
            $serie_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Serie'];
            $tipo_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Tipo'];

            //atualizando dados da ultima nota emitida
            $empresaSessao->num_ultima_nota = $numero_rps;
            $empresaSessao->save();
            
            $nota = new \stdClass();
            $nota->num_nfse = $numero_nota;
            $urlNota = $this->consultarUrlNotaApi($nota, $empresaSessao);

            //salvar tomador e depois salvar nota
            $dadosTomador = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['TomadorServico'];
            $tomador = $this->salvarTomador($dadosTomador, $empresaSessao);

            $novaNota = [
                'empresa_id' => $empresaSessao->id,
                'tomador_id' => $tomador->id,
                'num_nfse' => $numero_nota,
                'numero_rps' => $numero_rps,
                'cod_verificacao_nfse' =>  $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['CodigoVerificacao'],
                'data_emissao_nfse' => date('Y-m-d', strtotime((string)  $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['DataEmissao'])),
                'competencia' => (string) $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Competencia'],
                'valor_nota' => (float) $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['ValoresNfse']['ValorLiquidoNfse'],
                'cod_trib_mun' =>(string)$declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Servico']['CodigoTributacaoMunicipio'],
                'url_view' => $urlNota
            ];

            $notaFiscalServico = new NotaEmitida();
            $notaFiscalServico->adicionarNfse($novaNota);

            //fim salvar tomador

            $retorno =  [
                'error' => 0,
                'data' => [
                    'numero_nota' => $numero_nota,
                    'numero_rps' => $numero_rps,
                    'serie' => $serie_rps,
                    'tipo' => $tipo_rps,
                    'url_nota' => $urlNota
                ],
                'message' => $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['Protocolo']
            ];

            //Utilitarios::sendMessage("Nota Emitida via API pelo Cliente: ". $empresaSessao->razao_social);
        }

        return $retorno;
    }

    protected function gerarRPS($dados){    
        $empresaSessao = $this->empresaModel
            ->where('cpf_cnpj', $dados['prestador']['cpf_cnpj'])
            ->where('inscricao_municipal', $dados['prestador']['inscricao_municipal'])
            ->first();
        
        $codigoCnaePrincipal = $empresaSessao->cnaes()->find($empresaSessao->empresa_cnae_id)->codigo_cnae;
        $codigoAtividadeMunicipio = $empresaSessao->atividadesEmpresa()->find($empresaSessao->empresa_atividade_id)->codigo_atividade;

        //Construção do RPS
        $rps = new RpsClass();
        $tipoDoc = ($empresaSessao->getTipoPessoa($empresaSessao->cpf_cnpj) == 2 ? $rps::CNPJ: $rps::CPF);
        $rps->prestador($tipoDoc, $empresaSessao->cpf_cnpj, $empresaSessao->inscricao_municipal);
        
        //dados do tomador
        $tomador = new Tomador();
        $tipoDoc = ($tomador->getTipoPessoa($dados['tomador']['cpf_cnpj']) == 2 ? $rps::CNPJ: $rps::CPF);
        $rps->tomador($tipoDoc, 
            Utilitarios::limparCpfCnpj($dados['tomador']['cpf_cnpj']),
            is_null($dados['tomador']['inscricao_municipal']) ? '' : $dados['tomador']['inscricao_municipal'],
            htmlspecialchars($dados['tomador']['razao_social']),
            $dados['tomador']['telefone'],
            $dados['tomador']['email']
        );
        
        //Tomador Endereço
        $rps->tomadorEndereco(
            $dados['tomador']['endereco']['logradouro'],
            empty($dados['tomador']['endereco']['numero']) ? 'S/N' : $dados['tomador']['endereco']['numero'],
            Utilitarios::somenteLetrasENumerosSemSimbolos( $dados['tomador']['endereco']['complemento'] ),
            Utilitarios::somenteLetrasENumerosSemSimbolos($dados['tomador']['endereco']['bairro']),
            $dados['tomador']['endereco']['cidade_id'],
            $dados['tomador']['endereco']['uf_sigla'],
            $dados['tomador']['endereco']['cep']
        );

        $rps->numero($empresaSessao->num_ultima_nota + 1);

        $rps->serie(8);
        $rps->status($rps::STATUS_NORMAL);
        $rps->tipo($rps::TIPO_RPS);

        //reter iss
        if( (!isset($dados['dados_nota']['iss_retido'])) || (isset($dados['dados_nota']['iss_retido']) && $dados['dados_nota']['iss_retido'] == $rps::NAO)){
            $rps->issRetido($rps::NAO);
        }else{
            $rps->issRetido($rps::SIM);
        }

        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $rps->dataEmissao(new \DateTime("now", $timezone));

        $rps->municipioPrestacaoServico($dados['dados_nota']['municipio_prestacao_servico']);
                
        switch($dados['dados_nota']['natureza_operacao']){
            case 1: 
                $rps->naturezaOperacao($rps::NATUREZA_INTERNA);
                $rps->municipioIncidencia( $empresaSessao->is_mei == 1 ? $empresaSessao->cidade_id : $dados['tomador']['endereco']['cidade_id']);
                break;
            case 2: 
                $rps->naturezaOperacao($rps::NATUREZA_EXTERNA);
                $rps->municipioIncidencia($tomador->cidade_id);
                break;
            default:
                $rps->naturezaOperacao($rps::NATUREZA_INTERNA);
                break;
        }
        
        if(strlen($empresaSessao->item_lc_id) == 4){
            $codigoItemLc = substr($empresaSessao->item_lc_id,0,2).'.'.substr($empresaSessao->item_lc_id,2,2);
        }else{
            if($empresaSessao->item_lc_id < 1000){
                $res = (string) str_replace('.', '', $empresaSessao->item_lc_id / 1000);
            }
            $codigoItemLc = substr($res,0,2).'.'.substr($res,2,2);
        }

        $rps->itemListaServico($codigoItemLc);
        $rps->codigoCnae($codigoCnaePrincipal);
        $rps->codigoTributacaoMunicipio($codigoAtividadeMunicipio); //atividade exercida

        //Descrição dos Serviços
        $rps->discriminacao($dados['dados_nota']['descricao_servico']);//
        $rps->regimeEspecialTributacao($empresaSessao->regime_esp_tributacao); 
       
        //se for rps para substituição
        //$rps->rpsSubstituido('5555', 'A1', 1);

        $rps->optanteSimplesNacional( ($empresaSessao->is_optante_simples_nac == 1) ? $rps::SIM : $rps::NAO);
        $rps->incentivadorCultural($rps::NAO);

        //Log::info($dados['dados_nota']);
        
        //Valores dos Serviço
        $aliquota                       = $dados['dados_nota']['aliquota'];
        $totalServico                   = $dados['dados_nota']['valor_total'];
        $valorDeducoes 					= isset($dados['valor_deducao_base_calculo']) ? $dados['valor_deducao_base_calculo'] : 0.00;
        $valorPIS                       = $dados['dados_nota']['valor_pis'];
        $valorCofins                    = $dados['dados_nota']['valor_cofins'];
        $valorCSLL                      = $dados['dados_nota']['valor_csll'];
        $valorIRPF                      = $dados['dados_nota']['valor_irrf'];
        $valorInss                      = $dados['dados_nota']['valor_outros'];
        $valorOutrasRetencoes           = $dados['dados_nota']['valor_outras_retencoes'];
        $valorDescontoCondicionado      = isset($dados['dados_nota']['valor_desconto_condicionado']) ? $dados['dados_nota']['valor_desconto_condicionado'] : 0.00;
        $valorDescontoIncondicionado    = isset($dados['dados_nota']['valor_desconto_incondicionado']) ? $dados['dados_nota']['valor_desconto_incondicionado'] : 0.00;
      
        $rps->aliquota($aliquota);
        $rps->valorServicos($totalServico);
        $rps->baseCalculo($totalServico -$valorDeducoes);
        $calculoValorIss = $totalServico * ($aliquota / 100);
        $rps->valorDeducoes($valorDeducoes);

        $rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
       
        //retenções de impostos
        $rps->valorPis($valorPIS);
        $rps->valorCofins($valorCofins);
        $rps->valorCsll($valorCSLL);
        $rps->valorInss($valorInss);
        $rps->valorIr($valorIRPF);
        $rps->outrasRetencoes($valorOutrasRetencoes);
        $rps->descontoCondicionado($valorDescontoCondicionado);
        $rps->descontoIncondicionado($valorDescontoIncondicionado);

        $totalRetencoesImpostos = $valorPIS; 
        $totalRetencoesImpostos += $valorCofins; 
        $totalRetencoesImpostos += $valorInss; 
        $totalRetencoesImpostos += $valorIRPF; 
        $totalRetencoesImpostos += $valorCSLL; 
        $totalRetencoesImpostos += $valorOutrasRetencoes;

        if(isset($dados['iss_retido'])){
            $totalRetencoesImpostos += $calculoValorIss;
        }

        $totalRetencoesImpostos += $valorDescontoCondicionado;
        $totalRetencoesImpostos += $valorDescontoIncondicionado;
        $rps->valorTotalTributos = $totalRetencoesImpostos;
        $valorFinalNota = $totalServico - $totalRetencoesImpostos;
        $rps->valorLiquidoNfse($valorFinalNota);

        return $rps;
    }

    public function cancelarNota(Request $request)
    {
        $dados = $request->all();
        
        if(!isset($dados['num_nfse']) || empty($dados['num_nfse'])){
            return response()->json([
                'error' => 1,
                'data' => null,
                'message' => 'Número NFSe não Informado.'
            ]);
        }

        $mensagens = [
            'prestador.cpf_cnpj.required' => 'O Campo :attribute deve ser informado',
            'prestador.cpf_cnpj.min' => 'O Campo :attribute deve ser informado com 14 digitos.',
            'prestador.cpf_cnpj.max' => 'O Campo :attribute deve ser informado com 14 digitos.',
            'num_nfse' => 'O Campo Número da NFSe deve ser informado.',
        ];
        $validator = Validator::make($dados, [
            'prestador' => 'array|required',
            'prestador.cpf_cnpj' => 'required|min:14|max:14',
            'prestador.inscricao_municipal' => 'required|numeric',
            'num_nfse' => 'numeric|required',
            'motivo_cancelamento' => 'numeric|required',
        ], $mensagens);
 
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => [
                   'errors' => json_decode($validator->errors()->toJson())
                ],
                'message' => 'Erro de Validação de Dados'
            ]);
        }

        $userId = auth()->user()->id;

        $empresaSessao = $this->empresaModel
            ->where('cpf_cnpj', $dados['prestador']['cpf_cnpj'])
            ->where('inscricao_municipal', $dados['prestador']['inscricao_municipal'])
            ->where('user_id', $userId)
            ->first();

        if(!empty($empresaSessao)){
            $cancelamento = $this->cancelarNfseApi($dados, $empresaSessao);
            if(isset($cancelamento->RetCancelamento->NfseCancelamento->Confirmacao)){
                //$dados['num_nfse']

                $nota = $this->nfseModel
                    ->where('empresa_id', $empresaSessao->id)
                    ->where('num_nfse', $dados['num_nfse'])
                    ->first();
                
                $nota->cancelada = 1;
                $nota->motivo_cancelamento = 'Nota Cancelada Via API';
                $nota->data_hora_cancel = date('Y-m-d G:i:s');
                $nota->save();
                

                //Utilitarios::sendMessage("Nota Cancelada via API pelo Cliente: ". $empresaSessao->razao_social);

                return response()->json([
                    'error' => 0,
                    'data' => null,
                    'message' => 'Nota Cancelada com Sucesso.'
                ]);
            }else{
                $erro = $cancelamento->ListaMensagemRetorno->MensagemRetorno;
                $mensagem = "Erro: #" . $erro->Codigo. ' - ' . $erro->Mensagem;
                $mensagem .= ' Solução: Para Corrigir o Erro ' . $erro->Correcao;

                return response()->json([
                    'error' => 1,
                    'data' => $mensagem,
                    'message' => 'A nota fiscal de serviço não pode ser cancelada.'
                ]);
            }
        }else{
            return response()->json([
                'error' => 1,
                'data' => null,
                'message' => 'Empresa Emitente Não Encontrada.'
            ]);
        }
    }

    protected function salvarTomador($dadosTomador, $empresaSessao){
        if(isset($dadosTomador['IdentificacaoTomador']['CpfCnpj']['Cnpj'])){
            $docTomador = $dadosTomador['IdentificacaoTomador']['CpfCnpj']['Cnpj'];
        } else {
            $docTomador = $dadosTomador['IdentificacaoTomador']['CpfCnpj']['Cnpj']['Cpf'];
        }

        $tomador = $this->tomadorModel
            ->where('empresa_id', $empresaSessao->id)
            ->where('cpf_cnpj', $docTomador)
            ->first();

        if(is_null($tomador)){            
            $tomador = new Tomador();
            $tomador->razao_social = $dadosTomador['RazaoSocial'];
            $tomador->cpf_cnpj = $docTomador;
            $tomador['empresa_id'] = $empresaSessao->id;
            $tomador->cep = $dadosTomador['Endereco']['Cep'];
            $tomador->logradouro = $dadosTomador['Endereco']['Endereco'];
            $tomador->numero = $dadosTomador['Endereco']['Numero'];
            $tomador->complemento = $dadosTomador['Endereco']['Complemento'];
            $tomador->bairro = $dadosTomador['Endereco']['Bairro'];
            $tomador->cidade_id = $dadosTomador['Endereco']['CodigoMunicipio'];
            $tomador->telefone1 = $dadosTomador['Contato']['Telefone'];
            $tomador->telefone2 = $dadosTomador['Contato']['Telefone'];
            $tomador->email = isset($dadosTomador['Contato']['Email']) && !empty($dadosTomador['Contato']['Email']) ? $dadosTomador['Contato']['Email'] : null;
            $tomador->save();

            $tomador = $this->tomadorModel->where('cpf_cnpj', $docTomador)->first();
        }

        return $tomador;
    }
}
