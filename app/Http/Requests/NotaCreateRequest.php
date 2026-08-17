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
            'txtTotalValorLiquido' => [
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
            'txtDescServicos' => [
                'required',
                'string',
                'max:2000'
            ],

            'txtInfoComplementares' => [
                'required',
                'string',
                'max:2000'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'txtTotalValorLiquido.required' =>
                'O valor líquido é obrigatório.',

            'txtTotalValorLiquido.regex' =>
                'Informe um valor válido no formato 10,00 ou 1.000,00.',

            'txtDescServicos.required' =>
                'A descrição dos serviços é obrigatória.',
        ];
    }
}
