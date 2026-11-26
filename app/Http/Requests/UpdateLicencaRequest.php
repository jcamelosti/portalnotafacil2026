<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicencaRequest extends FormRequest
{
    public function authorize()
    {
        // Acesso ao admin já é controlado pelo middleware 'controle.acesso'.
        return true;
    }

    public function rules()
    {
        return [
            'plano_id' => ['required', 'integer', 'exists:planos,id'],
            'validate' => ['required', 'date_format:Y-m-d'],
            'observacao' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes()
    {
        return [
            'plano_id' => 'plano',
            'validate' => 'nova validade',
            'observacao' => 'observação',
        ];
    }
}
