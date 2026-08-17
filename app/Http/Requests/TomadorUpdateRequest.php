<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TomadorUpdateRequest extends FormRequest
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
            'inscricao_municipal' => ['nullable','numeric'],
            'cidade_id' => ['required', 'numeric', 'gt:0'],
            'complemento' => ['required', 'string', 'max:60'],
        ];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages(){
        return [
            'inscricao_municipal.numeric' => 'A Inscrição Municipal deve possuir somente números.',
            'cidade_id.required' => 'Informe a Cidade',
            'cidade_id.gt' => 'Informe a Cidade',
            'complemento.max' => 'O Complemento deve ter no máximo 60 caracteres.'
        ];
    }
}
