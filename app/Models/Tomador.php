<?php

namespace App\Models;

use App\Utilitarios\Utilitarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Tomador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tomadores';
    
    protected $fillable = [
        'empresa_id',
        'razao_social',
        'nome_fantasia',
        'email',
        'cpf_cnpj',
        'inscricao_municipal',
        'telefone1',
        'telefone2',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade_id',
        'codigo_pais_bacen',
        'nif'
    ];

    /*protected static function booted()
    {
        static::creating(function ($model) {
            $model->empresa_id = session()->get('empresa_selecionada');
        });
    }*/


    /*public function getCepAttribute($value)
    {
        return Utilitarios::formatar('cep', $value);
    }*/

    public function getCpfCnpjFmtAttribute(){
        $doc = null;
        
        if(isset($this->attributes['cpf_cnpj'])){
            $doc = $this->attributes['cpf_cnpj'];
            if(strlen($doc) == 11){
                $doc = Utilitarios::formatar('cpf', $doc);
            }else{
                $doc = Utilitarios::formatar('cnpj', $doc);
            }
        }
        
        return $doc;
    }

    public function setCpfCnpjAttribute($value)
    {
        if ($value != null) {
            $this->attributes['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $value);
        }
    }

    public function getTipoPessoa($cpf_cnpj)
    {
        return (strlen($cpf_cnpj) <= 12) ? 1 : 2;
    }

    public function cidade()
    {
        return $this->belongsTo(Municipio::class, 'cidade_id', 'codigo');
    }

    public function tomadoresList($empresaId){
        return ['' =>'Selecione o Tomador'] + $this
            ->select(
                'id',
                DB::raw("concat(cpf_cnpj, ' - ', IFNULL(razao_social, '')) as field1")
            )
            ->where('empresa_id', $empresaId)
            ->orderBy('razao_social', 'asc')
            ->pluck('field1', 'id')
            ->all();
    }
}
