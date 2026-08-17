<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'razao_social',
        'dt_nasc',
        'cpf_cnpj',
        'cep',
        'numero',
        'endereco',
        'complemento',
        'bairro',
        'cidade_id',
        'telefone1',
        'telefone2',
    ];

    //Accessor - Na Visualização
    public function getDtNascAttribute($value){
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    
    /*public function setDtNascAttribute($value){
        $this->attributes['dt_nasc'] = \DateTime::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }*/

    public function getClientes(){
        return [null =>'Selecione o Cliente'] + $this
                ->select('clientes.id as id',DB::raw("concat(clientes.cpf_cnpj, ' - ', clientes.nome) as nome"))
                ->orderBy('nome', 'asc')
                ->pluck('nome', 'id')
                ->all();
    }

    public function getClientesString(){
        $lista =  $this
            ->select('clientes.id as id',DB::raw("concat(clientes.nome, ' - ', clientes.cpf_cnpj) as nome"))
            ->orderBy('nome', 'asc')
            ->pluck('nome', 'id')
            ->all();

        $string = '';
        foreach($lista as $key => $data){
            $string.="$key:\"$data\",";
        }
        $string = substr($string,0, strlen($string) - 1);

        return $string;
    }

    protected function formatarCpfCnpj($doc) {
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $qtd = strlen($doc);

        if($qtd >= 11) {
            if($qtd === 11 ) {
                $docFormatado = substr($doc, 0, 3) . '.' .
                    substr($doc, 3, 3) . '.' .
                    substr($doc, 6, 3) . '.' .
                    substr($doc, 9, 2);
            } else {
                $docFormatado = substr($doc, 0, 2) . '.' .
                    substr($doc, 2, 3) . '.' .
                    substr($doc, 5, 3) . '/' .
                    substr($doc, 8, 4) . '-' .
                    substr($doc, -2);
            }
            return $docFormatado;
        }
    }

    public function cidade()
    {
        return $this->belongsTo(Municipio::class, 'cidade_id', 'codigo');
    }
}
