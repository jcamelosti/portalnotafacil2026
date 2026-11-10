<?php

namespace App\Services\FocuNfe\Municipios;

use App\Models\Empresa;
use App\Services\FocuNfe\Contracts\FocunfseMunicipioInterface;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;

class Municipio5200308 implements FocunfseMunicipioInterface
{
    public function gerarPayload(
        Empresa $empresa,
        DPSDataSnDTO $dados
    ): array {
        $dadosSend = [
            'data_emissao' => now()->format('Y-m-d\TH:i:sO'),
            'data_competencia' => $dados->dataEmissao,//$dados['data_competencia'] ?? now()->format('Y-m-d'),
            'codigo_municipio_emissora' => (int) $empresa->cidade_id,
            'cnpj_prestador' => $empresa->cpf_cnpj,
            'inscricao_municipal_prestador' => $empresa->inscricao_municipal,
            'codigo_opcao_simples_nacional' => (int) $empresa->op_simp_nac,
            'regime_tributario_simples_nacional' => (int) $empresa->tp_reg_apuracao_sn,
            'regime_especial_tributacao' => (int) $empresa->tp_regime_esp_trib_mun,
            
            //dados do tomador
            'cnpj_tomador' => $dados->cnpjTomador,
            'razao_social_tomador' => $dados->razaoTomador,
            'codigo_municipio_tomador' => (int) $dados->codigoMunicipioTomador,
            'cep_tomador' => $dados->cepTomador,
            'logradouro_tomador' => $dados->cepTomador,
            'numero_tomador' => $dados->numeroTomador,
            'complemento_tomador' => $dados->complementoTomador,
            'bairro_tomador' => $dados->bairroTomador,
            'telefone_tomador' => $dados->foneTomador,
            'email_tomador' => $dados->emailTomador,

            'codigo_municipio_prestacao' => (int) $dados->codigoMunicipioPrestacao,
            'codigo_tributacao_nacional_iss' => (string) $dados->codigoTributacaoNacional,
            'descricao_servico' => $dados->descricaoServico,
            'valor_servico' => (float) ($dados->valorServico ?? $dados->valorServico),

            'percentual_aliquota_relativa_municipio' => (float) $dados->percentualTotalTributos,
            'tributacao_iss' => (int) ($dados->tributaIss ?? 1),
            'tipo_retencao_iss' => (int) ($dados->tipoRetencaoIss ?? 1),

            'indicador_total_tributacao' => (string) $dados->cIndOp,
            'codigo_nbs' => (string) $dados->codigoNbs,
        ];

        return $dadosSend;
    }
}
