<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'empresas' => $this->empresas->map(fn ($empresa) => [
                'id' => $empresa->id,
                'cpf_cnpj' => $empresa->cpf_cnpj,
                'nome' => $empresa->nome_fantasia,
                'created_at' => $empresa->created_at->format('Y-m-d H:i:s'),
            ]),
        ];
    }
}
