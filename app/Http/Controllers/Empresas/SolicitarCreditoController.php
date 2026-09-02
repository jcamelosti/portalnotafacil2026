<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Fatura;
use App\Models\PlanoVariacao;
use App\Models\Servico;
use App\Utilitarios\Utilitarios;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\isNull;

class SolicitarCreditoController extends Controller
{
    private $faturaModel;
    public function __construct(Fatura $faturaModel)
    {
        $this->middleware('auth');
        $this->faturaModel = $faturaModel;
    }

    public function solicitarCredito(){
        $valores = [];

        if (collect([56,78,79,86])->contains(auth()->id())) {
            //combinado com blessed - quando precisar ajustar ajustar no valor
            $planosValores = PlanoVariacao::where('plano_id', 2)->first();
        }else{
            $planosValores = PlanoVariacao::where('plano_id', 4)->first();
        }
        
        for($i = 5; $i <= 20; $i++){
            //if( ($i % 2) == 0){
                $valores[number_format($i * $planosValores->valor, 2, ',', '.')] = $i . ' licenças - R$ ' . number_format($i * $planosValores->valor, 2, ',', '.');
            //}
        }
        
       
        return view('solicitarcredito.solicitarcredito', compact('valores'));
    }

    //infinite pay
    /*public function store(Request $request){
        $data = $request->all();
        $valorVenda = (float)str_replace(',', '.', str_replace('.', '', $data['valor_credito']));
        $empresa = Empresa::where('user_id', Auth::user()->id)->first();
        $cliente = $empresa
            ->responsavel()
            ->first()
            ->cliente()
            ->first();

        $qtdEmpresasUser = Empresa::where('user_id', Auth::user()->id)->count();
        if($qtdEmpresasUser > 1 && (int)auth()->user()->can_insert_credit == 0){
            $cliente = null;
        }
        
        if(is_null($cliente)){
            $cliente = new Cliente();
            $cliente->razao_social = $empresa->razao_social;
            $cliente->cpf_cnpj = $empresa->cpf_cnpj;
            $cliente->telefone1 = $empresa->telefone1;
            $cliente->cep = $empresa->cep;
            $cliente->endereco = $empresa->logradouro;
            $cliente->numero = $empresa->numero;
            $cliente->complemento = $empresa->complemento;
            $cliente->bairro = $empresa->bairro;
            $cliente->cidade_id = $empresa->cidade_id;
        }

        $fatura = $this->faturaModel->create([
            'user_id' => Auth::user()->id,
            'empresa_id' => $empresa->id,
            'fatura_status_id' => 1,
            'total' => $valorVenda,
            'transacao_id' => null,
            'num_doc' => $this->faturaModel->getNumDoc(),
        ]);

        $centavos = (int) round($fatura->total * 100);

        $url = 'https://api.infinitepay.io/invoices/public/checkout/links';
        $dados =  [
            "handle" => "josue-24685881-9n8",
            "items" => [
                [
                    "quantity" => 1,
                    "price" => $centavos, //em centavos
                    "description" => 'INSERÇÃO DE CRÉDITO Portal Nota Fácil',
                ]
            ],
            'customer' => [
                'name' => $cliente->razao_social,
                'email' => auth()->user()->email,
                'phone_number' => '+55'. $cliente->telefone1
            ],
            'redirect_url' => "https://https://nfse-nacional.portalnotafacil.com.br/c/pagamento-realizado",
            'webhook_url'=> 'https://https://nfse-nacional.portalnotafacil.com.br/webhook/infinitepay/capture',
            'address' => [
                "cep" => $cliente->cep,
                "street"=> $cliente->endereco,
                "neighborhood"=> $cliente->bairro,
                "number"=> $cliente->numero,
                "complement"=> $cliente->complemento. ' - ' . $empresa->cidade()->first()->municipio .  '/' . $empresa->cidade()->first()->estado->sigla,
            ],
            "order_nsu" => $fatura->num_doc,
        ];

        $response = Http::asJson()->post($url, $dados);
        $result = $response->json();
        
        return redirect()->away($result['url']);
    }*/

    //Pagamento de Inserção de Crédito via Mercado Pago
    public function store(Request $request){
        $data = $request->all();
        $valorVenda = (float)str_replace(',', '.', str_replace('.', '', $data['valor_credito']));
        
        $empresa = Empresa::where('user_id', Auth::user()->id)->first();
        $cliente = $empresa
            ->responsavel()
            ->first()
            ->cliente()
            ->first();

        if(is_null($cliente)){
            $cliente = new Cliente();
            $cliente->cpf_cnpj = $empresa->cpf_cnpj;
            $cliente->telefone1 = $empresa->telefone1;
            $cliente->cep = $empresa->cep;
            $cliente->endereco = $empresa->logradouro;
            $cliente->numero = $empresa->numero;
            $cliente->complemento = $empresa->complemento;
            $cliente->bairro = $empresa->bairro;
            $cliente->cidade_id = $empresa->cidade_id;
        }
        
        $fatura = $this->faturaModel->create([
            'user_id' => Auth::user()->id,
            'empresa_id' => $empresa->id,
            'fatura_status_id' => 1,
            'total' => $valorVenda,
            'transacao_id' => null,
            'num_doc' => $this->faturaModel->getNumDoc(),
        ]);


        //gerando servico para emitir nota fiscal nacional mei
        //não emita para contabilidade, depois emito manualmente para não gerar confusão de notas fiscais de serviço com a contabilidade
        if (!collect([56,78,79,86])->contains(auth()->id())) {
            Servico::create([
                'empresa_id' => 1, //id da minha empresa 24685881000190
                'fatura_id' => $fatura->id,
                'empresa_cliente_id' => $empresa->id,
                'local_prestacao' => 5201108, //Anápolis por Default
                'cod_trib_nacional_id' => 111,
                'nbs_id' => 916,
                'valor_servico' => $valorVenda,
                'descricao' => 'Ref. Licença de Uso Sistema Emissor Notas Fiscais'
            ]);
        }

        /*
        //PIX DINAMICO
        $sendData = [
            'IsSandbox' => false,
            'Application' => 'Portal Nota Fácil',
            'Vendor' => 'Portal Nota Fácil',
            'PaymentMethod' => '6',
            "Reference"     => $fatura->num_doc,
            "CallbackUrl"   => 'https://emissor.portalnotafacil.com.br/retorno/safe2pay',
            'Customer' => [
                'Name' => ucwords(strtolower(Auth::user()->name)),
                'Identity' => preg_replace('/[^0-9]/', '', $cliente->cpf_cnpj),
                'Phone' => preg_replace('/[^0-9]/', '', $cliente->telefone1),
                'Email' => Auth::user()->email,
                'Address' =>
                   [
                        'ZipCode' => preg_replace('/[^0-9]/', '', $cliente->cep),
                        'Street' => $cliente->endereco,
                        'Number' => $cliente->numero,
                        'Complement' => $cliente->complemento,
                        'District' => $cliente->bairro,
                        'CityName' => $cliente->cidade()->first()->municipio,
                        'StateInitials' => $cliente->cidade()->first()->estado()->first()->sigla,
                        'CountryName' => 'Brasil',//Fixo
                    ],
            ],
            'Products' => [
                [
                    'Code' => '9999',
                    'Description' => 'Crédito Avulso - Portal Nota Fácil',
                    'UnitPrice' => number_format($valorVenda, 2),
                    'Quantity' => 1,
                ],
            ],
        ];

        $pix = json_encode($sendData);
        $opts = array(
            'http'=>array(
                'method'=>"POST",
                'header'=>"X-API-KEY: 81A153D2CF054DB3B67906F533A1BC58\r\n" .
                    "Content-type: application/json\r\n",
                'content'=> $pix
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents('https://payment.safe2pay.com.br/v2/Payment', false, $context);

        if ($result === FALSE) {
            session()->flash('danger', 'Não foi possível Gerar o PIX.');
            return redirect()->route('dashboard.cliente');
        }
        $retorno = json_decode($result);

        $fatura->transacao_id = $retorno->ResponseDetail->IdTransaction;
        $fatura->safe2pay_pix_data = json_encode($retorno);
        $fatura->save();

        //Utilitarios::sendMessage('Cliente Portal Nota Fácil ' . Auth::user()->name . ' Solicitou Crédito de R$' . number_format($data['valor_credito'], 2, ',', '.'));
        $naoMostra = false;
        return view('licenciamento.chave_pix', compact('retorno', 'fatura', 'naoMostra'));*/
        
        try{
            $dados = [
                'Customer' => [
                    'Name' => ucwords(strtolower($cliente->razao_social)),
                    'Identity' => preg_replace('/[^0-9]/', '', $cliente->cpf_cnpj),
                    'Phone' => preg_replace('/[^0-9]/', '', $cliente->telefone1),
                    'Email' => Auth::user()->email,
                    'Address' =>
                        [
                            'ZipCode' => preg_replace('/[^0-9]/', '', $cliente->cep),
                            'Street' => $cliente->endereco,
                            'Number' => isNull($cliente->numero) ? '0' : $cliente->numero,
                            'Complement' => $cliente->complemento,
                            'District' => $cliente->bairro,
                            'CityName' => $cliente->cidade()->first()->municipio,
                            'StateInitials' => $cliente->cidade()->first()->estado()->first()->sigla,
                            'CountryName' => 'Brasil',//Fixo
                        ],
                ],
                'produto' => [
                    'descricao' => 'CRÉDITO - PORTAL NOTA FÁCIL',
                    'valor_unitario' => number_format($fatura->total,2),
                ],
            ];

            if(empty($fatura->qrcode_digitavel)){
                $retorno = $this->cobrarViaPixMercadoPago($dados);

                if($retorno['success']){
                    $ret = $retorno;
                    $retorno = $retorno['data'];
    
                    $fatura->transacao_id = $ret['referencia'];
                    $fatura->safe2pay_pix_data = '-';
                    $fatura->qrcode_digitavel = $retorno->point_of_interaction->transaction_data->qr_code;
                    $fatura->qrcode_base64 = $retorno->point_of_interaction->transaction_data->qr_code_base64;
                    $fatura->save();    
                    
                    return view('licenciamento.chave_pix', compact([
                        'fatura'
                    ]));
                }else{
                    session()->flash('info', 'O seu pedido não pode ser processado. Confira as informações do pedido.');
                    return redirect()->route('area-cliente');
                }
            }else{
                return view('licenciamento.chave_pix', compact([
                    'fatura'
                ]));
            }           
        }catch(Exception $e){
            session()->flash('danger', 'Não foi possível gerar a Chave PIX. Tente Novamente');
            return redirect()->route('area-cliente');
        }
    }

    /*
        Cobrança via PIX MercadoPago
    */
    private function cobrarViaPixMercadoPago($dados)
    {
        $retorno = null;

        $nome = $dados['Customer']['Name'];
        $nomeArr = explode(' ', ucwords(strtolower($nome)));
        $nome = $nomeArr[0];
        unset($nomeArr[0]);

        $payment = [
            "transaction_amount" => (float)number_format($dados['produto']['valor_unitario'],2),
            "description" => $dados['produto']['descricao'],
            "payment_method_id" => "pix",
            "payer" => [
                "email" => $dados['Customer']['Email'],
                "first_name" => $nome,
                "last_name" => implode(' ', $nomeArr),
                "identification" => [
                    "type" => strlen(preg_replace('/[^0-9]/', '', $dados['Customer']['Identity'])) == 14 ? "CNPJ": "CPF",
                    "number" => preg_replace('/[^0-9]/', '', $dados['Customer']['Identity'])
                ],
                "address" => [
                    "zip_code" => preg_replace('/[^0-9]/', '', $dados['Customer']['Address']['ZipCode']),
                    "street_name" => $dados['Customer']['Address']['Street'],
                    "street_number" => $dados['Customer']['Address']['Number'],
                    "neighborhood" =>  $dados['Customer']['Address']['District'],
                    "city" => $dados['Customer']['Address']['CityName'],
                    "federal_unit" => $dados['Customer']['Address']['StateInitials'],
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/v1/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment));

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '. getenv('MERCADOPAGO_ACCESS_TOKEN');
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $result= json_decode($result);

        if(isset($result->id)){
            $retorno =  [
                'success' => TRUE,
                'data' => $result,
                'referencia' => $result->id
            ];
        }else{
            $retorno =  [
                'success' => FALSE,
                'data' => $result,
                'referencia' => null
            ];
        }

        return $retorno;
    }
}
