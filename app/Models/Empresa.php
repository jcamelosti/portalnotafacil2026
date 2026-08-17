<?php

namespace App\Models;

use App\Utilitarios\Utilitarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'empresas';
    protected $fillable = [
        'id_integracao',
        'user_id',
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
        'is_mei',
        'is_optante_simples_nac',
        'vig_ini_simples_nac',
        'vig_fim_simples_nac',
        'permite_deducao',
        'permite_desc_incond',
        'permite_desc_cond',
        'empresa_cnae_id',
        'empresa_atividade_id',
        'item_lc_id',
        'num_ultima_nota',
        'data_consulta_nota', //Ultima Data de Consulta de Nota
        'plano_id',
        'dados_cadastrais',
        'regime_esp_tributacao',
        'porte_empresa',
        'natureza_juridica',
        'serie_nota',
        'nbs_id',
        'num_ultimo_dps',
        'sigla_provedor',
        'ambiente_emissao'
    ];

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

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTipoPessoa($cpf_cnpj)
    {
        return (strlen($cpf_cnpj) <= 12) ? 1 : 2;
    }

    public function cnaes()
    {
        return $this->hasMany(EmpresaCnae::class, 'empresa_id', 'id');
    }

    public function atividadesEmpresa()
    {
        return $this->hasMany(EmpresaAtividade::class, 'empresa_id', 'id');
    }

    public function naturezaOperacoes()
    {
        return $this->hasMany(EmpresaNaturezaOperacao::class, 'empresa_id', 'id');
    }

    public function tomadores()
    {
        return $this->hasMany(Tomador::class, 'empresa_id', 'id');
    }

    public function notasEmitidas()
    {
        return $this->hasMany(NotaEmitida::class, 'empresa_id', 'id');
    }

    public function licenca()
    {
        return $this->hasOne(License::class, 'empresa_id', 'id');
    }

    public function cidade()
    {
        return $this->belongsTo(Municipio::class, 'cidade_id', 'codigo');
    }

    public function empresasList($userId = null){
        return ['' =>'Selecione a Empresa'] + $this
            ->select(
                'id',
                DB::raw("concat(cpf_cnpj, ' - ', IFNULL(razao_social, '')) as field1")
            )
            ->where(function($query) use ($userId) {
                if(!is_null($userId)){
                    //$query->where('user_id', $userId);
                    $query->where('user_id', $userId)
                    ->orWhereExists(function($sub) use ($userId) {
                        $sub->select(DB::raw(1))
                            ->from('empresas_compartilhadas')
                            ->whereColumn('empresas_compartilhadas.empresa_id', 'empresas.id')
                            ->where('empresas_compartilhadas.solicitante_user_id', $userId)
                            ->where('empresas_compartilhadas.autorizado', 'S');
                    });
                }
            })
            ->orderBy('razao_social', 'asc')
            ->pluck('field1', 'id')
            ->all();
    }

    public function itemLc(){
        return $this->belongsTo(ListaServico::class, 'item_lc_id', 'id');
    }

    public function getValidateLicencaAttribute()
    {
        $resultado = null;
        $licenca = $this->licenca()
            ->whereDate('validate', '>=', DB::raw('CURDATE()'))
            ->orderBy('id', 'DESC')
            ->first();
        
        if(!is_null($licenca)){
            $resultado = $licenca->validate_pt_br;
        }

        return $resultado;
    }

    public function plano(){
        return $this->belongsTo(Plano::class, 'plano_id', 'id');
    }

    public function getRegimeEspecialTributacao(){
        return [
            1 => 'Microempresa Municipal',
            2 => 'Estimativa',
            3 => 'Sociedade de Profissionais',
            4 => 'Cooperativa',
            5 => 'Microempresário Individual (MEI)',
            6 => 'Microempresa ou Empresa de Pequeno Porte (ME EPP)',
        ];
    }

    public static function getProvedorEmissao(){
        return [
            '' => 'Nenhum Provedor Selecionado',
            'issnet' => 'Nota Control - ISSNET',
        ];
    }

    public static function getAmbienteEmissao(){
        return [
            '' => 'Nenhum Ambiente Selecionado',
            'HOMOLOGACAO' => 'Homologação(Testes)',
            'PRODUCAO' => 'Produção'
        ];
    }
}


