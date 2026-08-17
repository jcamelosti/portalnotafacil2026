<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaEmitida extends Model
{
    use HasFactory;

    protected $table = 'emitidas';
    
    protected $casts = [
        'dados_emissao_json' => 'array',
    ];

    protected $fillable = [
        'empresa_id',
        'tomador_id',
        'num_nfse',
        'cod_verificacao_nfse',
        'data_emissao_nfse',
        'numero_rps',
        'serie_rps',
        'tipo_rps',
        'data_emissao_rps',
        'competencia',
        'valor_nota',
        'cod_trib_mun',
        'url_view',
        'cancelada',
        'data_hora_cancel',
        'motivo_cancelamento',
        'nome_tomador',
        'dados_emissao_json'
    ];

    public function setCodVerificacaoNfseAttribute($value)
    {
        $this->attributes['cod_verificacao_nfse'] = $this->mask(str_replace(' ', '', $value), '## ## ##');
    }

    public function getDataEmissaoNfseAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }

    public function getDataHoraCancelAttribute($value)
    {
        if ($value != '')
            return Carbon::createFromFormat('Y-m-d G:i:s', $value)->format('d/m/Y - G:i:s');
        else
            return null;
    }

    public function getCanCancelAttribute(){       
        $diff = now()->diffInDays($this->created_at);
        
        if($diff <= 90){
            return true;
        }

        return false;
    }

    public function getValorNotaAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tomador()
    {
        return $this->belongsTo(Tomador::class);
    }

    protected function notaExiste($dados)
    {
        $res = $this
            ->where('empresa_id', $dados['empresa_id'])
            ->where('tomador_id', $dados['tomador_id'])
            ->where('num_nfse', $dados['num_nfse'])
            ->first();

        if ($res) {
            return true;
        }

        return false;
    }
    public function adicionarNfse($nfse)
    {
        if (!$this->notaExiste($nfse)) {
            return $this->create($nfse);
        } else {
            $nota = $this->where('empresa_id', $nfse['empresa_id'])
                ->where('tomador_id', $nfse['tomador_id'])
                ->where('num_nfse', $nfse['num_nfse'])
                ->first();

            $nota = $nota->update($nfse);

            return $this->where('empresa_id', $nfse['empresa_id'])
                ->where('tomador_id', $nfse['tomador_id'])
                ->where('num_nfse', $nfse['num_nfse'])
                ->first();
        }
    }

    public function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
}
