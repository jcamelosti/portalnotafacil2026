<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndOpIbsCbsSeeder extends Seeder
{
    public function run(): void
    {
        $dados = [
            [
                'codigo' => '020101',
                'descricao' => 'Operação com bem imóvel, bem imaterial, inclusive direito, relacionada a bem imóvel',
                'local_operacao' => 'o local onde o imóvel estiver situado',
                'local_fornecimento' => 'Localidade do imóvel (1)',
                'ativo' => true,
            ],
            [
                'codigo' => '020201',
                'descricao' => 'Serviço prestado fisicamente sobre bem imóvel',
                'local_operacao' => 'o local onde o imóvel estiver situado',
                'local_fornecimento' => 'Localidade do imóvel (1)',
                'ativo' => true,
            ],
            [
                'codigo' => '020301',
                'descricao' => 'Serviço de administração e intermediação de bem imóvel',
                'local_operacao' => 'o local onde o imóvel estiver situado',
                'local_fornecimento' => 'Localidade do imóvel (1)',
                'ativo' => true,
            ],
            [
                'codigo' => '030101',
                'descricao' => 'Serviço prestado fisicamente sobre a pessoa ou fruído presencialmente por pessoa física',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Estabelecimento do fornecedor',
                'ativo' => true,
            ],
            [
                'codigo' => '030102',
                'descricao' => 'Serviço prestado fisicamente sobre a pessoa ou fruído presencialmente por pessoa física',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço do adquirente',
                'ativo' => true,
            ],
            [
                'codigo' => '030103',
                'descricao' => 'Serviço prestado fisicamente sobre a pessoa ou fruído presencialmente por pessoa física',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço do destinatário',
                'ativo' => true,
            ],
            [
                'codigo' => '030104',
                'descricao' => 'Serviço prestado fisicamente sobre a pessoa ou fruído presencialmente por pessoa física',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço diverso do fornecedor, adquirente ou destinatário',
                'ativo' => true,
            ],
            [
                'codigo' => '040101',
                'descricao' => 'Serviço de planejamento, organização e administração de feiras, exposições, congressos, espetáculos, exibições e congêneres',
                'local_operacao' => 'o local do evento a que se refere o serviço',
                'local_fornecimento' => 'Local do Evento',
                'ativo' => true,
            ],
            [
                'codigo' => '050101',
                'descricao' => 'Serviço prestado fisicamente sobre bem móvel material',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Estabelecimento do fornecedor',
                'ativo' => true,
            ],
            [
                'codigo' => '050102',
                'descricao' => 'Serviço prestado fisicamente sobre bem móvel material',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço do adquirente',
                'ativo' => true,
            ],
            [
                'codigo' => '050103',
                'descricao' => 'Serviço prestado fisicamente sobre bem móvel material',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço do destinatário',
                'ativo' => true,
            ],
            [
                'codigo' => '050104',
                'descricao' => 'Serviço prestado fisicamente sobre bem móvel material',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço diverso do fornecedor, adquirente ou destinatário',
                'ativo' => true,
            ],
            [
                'codigo' => '050201',
                'descricao' => 'Serviços portuários',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Local da prestação',
                'ativo' => true,
            ],
            [
                'codigo' => '060101',
                'descricao' => 'Serviço de transporte de passageiros',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Local de início do transporte',
                'ativo' => true,
            ],
            [
                'codigo' => '070101',
                'descricao' => 'Serviço de transporte de carga',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Endereço fornecido para entrega',
                'ativo' => true,
            ],
            [
                'codigo' => '070102',
                'descricao' => 'Serviço de transporte de carga',
                'local_operacao' => 'o local da prestação do serviço',
                'local_fornecimento' => 'Local da retirada',
                'ativo' => true,
            ],
            [
                'codigo' => '080101',
                'descricao' => 'Serviço de exploração de via',
                'local_operacao' => 'o território de cada Município e Estado, ou do Distrito Federal, proporcionalmente à correspondente extensão da via explorada',
                'local_fornecimento' => 'Local da prestação, correspondente à extensão da via explorada e proporcional ao território dos entes tributantes',
                'ativo' => true,
            ],
            [
                'codigo' => '100101',
                'descricao' => 'Cessão de espaço para prestação de serviços publicitários, em operações onerosas (4)',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio principal do adquirente (3)',
                'ativo' => true,
            ],
            [
                'codigo' => '100102',
                'descricao' => 'Cessão de espaço para prestação de serviços publicitários, em operações onerosas (4)',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio do destinatário, nos casos de adquirente residente ou domiciliado no exterior (5)(6)',
                'ativo' => true,
            ],
            [
                'codigo' => '100201',
                'descricao' => 'Cessão de espaço para prestação de serviços publicitários, em operações não onerosas (4)',
                'local_operacao' => 'o local do domicílio principal do: ... b) destinatário, nas operações não onerosas.',
                'local_fornecimento' => 'Local do domicílio principal do destinatário (6)',
                'ativo' => true,
            ],
            [
                'codigo' => '100301',
                'descricao' => 'Demais serviços, em operações onerosas',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio principal do adquirente (3)',
                'ativo' => true,
            ],
            [
                'codigo' => '100302',
                'descricao' => 'Demais serviços, em operações onerosas',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio do destinatário, nos casos de adquirente residente ou domiciliado no exterior (5)(6)',
                'ativo' => true,
            ],
            [
                'codigo' => '100401',
                'descricao' => 'Demais serviços, em operações não onerosas',
                'local_operacao' => 'o local do domicílio principal do: ... b) destinatário, nas operações não onerosas.',
                'local_fornecimento' => 'Local do domicílio principal do destinatário (6)',
                'ativo' => true,
            ],
            [
                'codigo' => '100501',
                'descricao' => 'Demais bens móveis imateriais, inclusive direitos, em operações onerosas',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio principal do adquirente (3)',
                'ativo' => true,
            ],
            [
                'codigo' => '100502',
                'descricao' => 'Demais bens móveis imateriais, inclusive direitos, em operações onerosas',
                'local_operacao' => 'o local do domicílio principal do: a) adquirente, nas operações onerosas; ...',
                'local_fornecimento' => 'Local do domicílio do destinatário, nos casos de adquirente residente ou domiciliado no exterior (5)(6)',
                'ativo' => true,
            ],
            [
                'codigo' => '100601',
                'descricao' => 'Demais bens móveis imateriais, inclusive direitos, em operações não onerosas',
                'local_operacao' => 'o local do domicílio principal do: ... b) destinatário, nas operações não onerosas.',
                'local_fornecimento' => 'Local do domicílio principal do destinatário (6)',
                'ativo' => true,
            ],
        ];

        DB::table('ind_op_ibs_cbs')->upsert(
            $dados,
            ['codigo'],
            [
                'descricao',
                'local_operacao',
                'local_fornecimento',
                'ativo',
            ]
        );
    }
}