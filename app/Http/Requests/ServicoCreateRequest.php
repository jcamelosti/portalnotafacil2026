<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicoCreateRequest extends FormRequest
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
            'local_prestacao' => 'required|integer|exists:municipios_ibge,codigo',
            'tomador_id' => 'required|exists:tomadores,id',
            'cod_trib_nacional_id' => 'required|exists:cod_tributacao_nacional,id',
            'nbs_id' => 'required|exists:nbs,id',
            'valor_servico' => 'required|numeric|min:1.00',
            'descricao' => 'required|string|max:2000',
        ];
    }

     /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages(){
        return [
           // mensagens required
            'local_prestacao.required' => 'O campo Local de Prestação é obrigatório.',
            'tomador_id.required' => 'O campo Tomador é obrigatório.',
            'cod_trib_nacional_id.required' => 'O Código Tributário é obrigatório.',
            'nbs_id.required' => 'O NBS é obrigatório.',
            'valor_servico.required' => 'O Valor do Serviço é obrigatório.',
            'descricao.required' => 'A Descrição do Serviço é obrigatória.',

            // outras validações
            'local_prestacao.exists' => 'Local de Prestação inválido.',
            'tomador_id.exists' => 'Tomador inválido.',
            'cod_trib_nacional_id.exists' => 'Código Tributário inválido.',
            'nbs_id.exists' => 'NBS inválido.',
            'valor_servico.numeric' => 'O Valor do Serviço deve ser numérico.',
            'valor_servico.min' => 'O Valor do Serviço não pode ser negativo.',
            'descricao.max' => 'A descrição deve ter no máximo 1000 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->valor_servico) {
            $valor = str_replace('.', '', $this->valor_servico); // remove milhar
            $valor = str_replace(',', '.', $valor); // troca decimal

            $this->merge([
                'valor_servico' => $valor
            ]);
        }
    }
}
