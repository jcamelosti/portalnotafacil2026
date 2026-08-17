<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class License extends Model
{
    use HasFactory;

    protected $table = 'licenses';

    protected $fillable = [
        'empresa_id',
        'validate'
    ];

    /*protected $casts = [
        'validate' => 'date',
    ];*/

    public function getValidatePtBrAttribute()
    {
       return Carbon::createFromFormat('Y-m-d', $this->validate)->format('d/m/Y');
    }

    public function getVencidaAttribute()
    {
        $hj = date('Y-m-d');
        if ($hj > $this->vig_fim) {
            return true;
        }
        return false;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function licenca($empresaId){
        $resultado = null;

        $licenca = $this->whereDate('validate', '>=', DB::raw('CURDATE()'))
            ->where('empresa_id', $empresaId)
            ->orderBy('id', 'DESC')
            ->first();
    
        if(!is_null($licenca)){
            $resultado = $licenca->validate;
        }

        return $resultado;
    }
}
