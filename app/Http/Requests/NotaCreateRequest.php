<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       return [
            'txtTotal' => [
                'required',
                'regex:/^\d{1,3}(?:\.\d{3})*,\d{2}$|^\d+,\d{2}$/',
                function ($attribute, $value, $fail) {
                    $valor = str_replace('.', '', $value);
                    $valor = str_replace(',', '.', $valor);

                    if ((float) $valor <= 0) {
                        $fail('O valor líquido deve ser maior que zero.');
                    }
                },
            ],

            'txtBaseCalc' => [
                'required',
                'regex:/^\d{1,3}(?:\.\d{3})*,\d{2}$|^\d+,\d{2}$/',
                function ($attribute, $value, $fail) {
                    $valor = str_replace('.', '', $value);
                    $valor = str_replace(',', '.', $valor);

                    if ((float) $valor <= 0) {
                        $fail('O valor líquido deve ser maior que zero.');
                    }
                },
            ],

            'cTribNac' => [
                'required'
            ],

            'nbs'=>[
                'required'
            ],

            'ddlSituacaoTributaria' => [
                'required'
            ],

            'ddlSitTribFederal' => [
                'required'
            ],

            'ddlTipoRetFederal' => [
                'required'
            ],

            'ddlIndicadorOperacao' =>[
                'required'
            ],

            'ddlClassificacaoTributaria' =>[
                'required'
            ],

            'txtDescServicos' => [
                'required',
                'string',
                'max:2000'
            ],

            'txtInfoComplementares' => [
                'nullable',
                'string',
                'max:2000'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cTribNac.required' => 'É necessário Informar o Código de Tributação Nacional',
            'nbs.required' => 'É necessário informar o NBS',

            'txtTotal.required' =>
                'O valor do Serviço é obrigatório.',

            'txtBaseCalc.regex' =>
                'O valor do cálculo da Base de Cálculo Informe um valor válido no formato 10,00 ou 1.000,00.',

            'txtDescServicos.required' =>
                'A descrição dos serviços é obrigatória.',

            'ddlSitTribFederal.required' => 'A Situação Tributária do PIS/COFINS deve ser informada.',
            'ddlTipoRetFederal.required' => 'O Tipo de Retenção do PIS/COFINS/CSLL deve ser informado.',
            'ddlIndicadorOperacao.required' => 'A Indicação da Operação deve ser informada.',
            'ddlSituacaoTributaria.required' => 'A Situação Tributária deve ser informada.',
            'ddlClassificacaoTributaria.required' => 'A Classificação Tributária deve ser informada.',
        ];
    }
}
