<?php

namespace App\Http\Controllers\Irpj;

use App\Http\Controllers\Controller;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index()
    {
        return view('irpj.index');
    }

    public function upload()
    {    
        $dadosNotas = [];

        if(request()->hasFile('arquivo')){
            $arq = request()->file('arquivo');
            $nameArquivo = $arq->getClientOriginalName();
            $destinationPath = storage_path('/relatorio-nfse/');
            $arq->move($destinationPath, $nameArquivo);

            $caminho = storage_path('/relatorio-nfse/').$nameArquivo;

            $za = new \ZipArchive();
            $za->open($caminho);

            for ($i=0; $i<$za->numFiles;$i++) {
                $infoData = $za->statIndex($i);
                $conteudo = $za->getFromName($infoData['name']);
                $conteudoArquivo = iconv(mb_detect_encoding($conteudo, mb_detect_order(), true), "UTF-8//IGNORE", $conteudo);
                
                $domxml = new \DOMDocument('1.0');
                $domxml->preserveWhiteSpace = false;
                $domxml->formatOutput = true;
                $domxml->loadXML($conteudoArquivo);

                $conteudoXml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $domxml->saveXML());
                $xml = new \SimpleXMLElement($conteudoXml);
                $array = json_decode(json_encode($xml), TRUE);    

                if(isset($array['CompNfse']['tcNfse']['tcInfNfse'])){
                    $nfse = $array['CompNfse']['tcNfse']['tcInfNfse'];
                    
                    $dadosNotas[] = [
                        'num_nota' => $nfse['tcNumero'],
                        'codigo_verificacao' => $nfse['tcCodigoVerificacao'],
                        'data_emissao' => date('d/m/Y', strtotime($nfse['tcDataEmissao'])),
                        'valor_liquido' => $nfse['tcServico']['tcValores']['tcValorLiquidoNfse'],
                        'prestador_cnpj' => $nfse['tcPrestadorServico']['tcIdentificacaoPrestador']['tcCpfCnpj']['tcCnpj'],
                        'inscricao_municipal' => $nfse['tcPrestadorServico']['tcIdentificacaoPrestador']['tcInscricaoMunicipal'],
                        'tomador_cnpj' => $nfse['tcTomadorServico']['tcIdentificacaoTomador']['tcCpfCnpj']['tcCnpj'],
                        'tomador_razao_social' => $nfse['tcTomadorServico']['tcRazaoSocial'],
                        'status_nfse' => isset($array['CompNfse']['NfseCancelamento']) ? 'Cancelada' : 'Normal',
                    ];
                }else{
                    $nfse = $array['ListaNfse']['CompNfse']['Nfse']['InfNfse'];
                    
                    if($nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['TomadorServico']['IdentificacaoTomador']['CpfCnpj']['Cnpj']){
                        $doc = $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['TomadorServico']['IdentificacaoTomador']['CpfCnpj']['Cnpj'];
                    }else{
                        $doc = $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['TomadorServico']['IdentificacaoTomador']['CpfCnpj']['Cpf'];
                    }

                    $dadosNotas[] = [
                        'num_nota' => $nfse['Numero'],
                        'codigo_verificacao' => $nfse['CodigoVerificacao'],
                        'data_emissao' => date('d/m/Y', strtotime($nfse['DataEmissao'])),
                        'valor_liquido' => $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['Servico']['Valores']['ValorServicos'],
                        'prestador_cnpj' => $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['Prestador']['CpfCnpj']['Cnpj'],
                        'inscricao_municipal' => $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['Prestador']['InscricaoMunicipal'],
                        'tomador_cnpj' => $doc,
                        'tomador_razao_social' => $nfse['DeclaracaoPrestacaoServico']['InfDeclaracaoPrestacaoServico']['TomadorServico']['RazaoSocial'],
                        'status_nfse' => isset($array['ListaNfse']['CompNfse']['NfseCancelamento']) ? 'Cancelada' : 'Normal',
                    ];
                }
            }

            $colunas = [
                'Número NFSE', 'Código Verificação', 'Data Emissão', 'Valor Líquido', 'Prestador CNPJ', 'IM Prestador', 'CNPJ Tomador',
                'Razao Social do Tomador', 'Status NFSE'
            ];

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=".$nameArquivo.'.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $callback = function() use($dadosNotas, $colunas) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $colunas, ';');

                foreach ($dadosNotas as $row) {
                    fputcsv($file, array(
                        $row['num_nota'], 
                        trim($row['codigo_verificacao']), 
                        $row['data_emissao'],
                        'R$ '.$row['valor_liquido'], 
                        Utilitarios::formatar('cnpj', $row['prestador_cnpj']), 
                        ' '.$row['inscricao_municipal'].' ',
                        Utilitarios::formatar('cnpj', $row['tomador_cnpj']), 
                        ' '.$row['tomador_razao_social'].' ',
                        $row['status_nfse']
                    ),';');
                }

                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            session()->flash('message', 'Nenhum arquivo foi informado.');
            return redirect()->back();
        }
    }
}
