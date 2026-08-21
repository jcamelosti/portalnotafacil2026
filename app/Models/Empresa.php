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
        'ambiente_emissao',
        'regime_tributario',
        'op_simp_nac',
        'tp_regime_esp_trib_mun',
        'tp_reg_apuracao_sn'
    ];

    /*public function getCepAttribute($value)
    {
        return Utilitarios::formatar('cep', $value);
    }*/

    public function getCidadeIdAttribute(){
        $cidade_id = $this->attributes['cidade_id'];
        
        if($this->attributes['ambiente_emissao'] === 'HOMOLOGACAO'){
            $cidade_id = '5002704'; // Se estive setado $this->attributes['ambiente_emissao'] === 'HOMOLOGACAO', em hmg só funciona com campo grande
        }

        return $cidade_id;
    }

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

    public function atividadesEmpresa()
    {
        return $this->hasMany(EmpresaAtividade::class, 'empresa_id', 'id');
    }

    /*public function naturezaOperacoes()
    {
        return $this->hasMany(EmpresaNaturezaOperacao::class, 'empresa_id', 'id');
    }*/

    public function tomadores()
    {
        return $this->hasMany(Tomador::class, 'empresa_id', 'id');
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

    //Situação perante Simples Nacional:
    public static function getOpcaoSimplesNacional(){
        return [
            '1' => 'Não Optante',
			'2' => 'Optante - Microempreendedor Individual (MEI)',
			'3' => 'Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP)'
        ];
    }

    public static function getRegimeApuracaoSimplesNacional(){
        return [
            '1' => 'Regime de apuração dos tributos federais e municipal pelo SN',
			'2' => 'Regime de apuração dos tributos federais pelo SN e o ISSQN pela NFS-e conforme respectiva legislação municipal do tributo',
			'3' => 'Regime de apuração dos tributos federais e municipal pela NFS-e conforme respectivas legilações federal e municipal de cada tributo'
        ];
    }

    public static function getTiposRegimeEspecialTributacaoMunicipio(){
        return [
            '0' => 'Nenhum',
			'1' => 'Ato Cooperado (Cooperativa)',
			'2' => 'Estimativa',
			'3' => 'Microempresa Municipal',
			'4' => 'Notário ou Registrador', 
			'5' => 'Profissional Autônomo',
			'6' => 'Sociedade de Profissionais'
        ];
    }
}


