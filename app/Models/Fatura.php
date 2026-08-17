<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fatura extends Model
{
    use HasFactory;

    protected $table = 'faturas';
    protected $fillable = [
        'user_id',
        'empresa_id',
        'fatura_status_id',
        'total',
        'transacao_id',
        'forma_pagamento_id',
        'num_doc',
        'safe2pay_pix_data',
        'qrcode_digitavel',
        'qrcode_base64'
    ];

    public function getTotalFmtAttribute($value){
        return number_format( $this->attributes['total'], 2, ',', '.');
    }

    public function items(){
        return $this->hasMany(FaturaItem::class);
    }

    /*public function notificacoes(){
        return $this->hasMany(Notificacao::class);
    }*/

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function statusFatura(){
        return $this->belongsTo(FaturaStatus::class, 'fatura_status_id', 'id');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }

    public function servico(){
        return $this->belongsTo(Servico::class, 'id', 'fatura_id');
    }
    
    public function getNumDoc(){
        $numdoc = time() + rand();
        $fnCheck = function($numChk) use (&$fnCheck, &$numdoc){
            $ok = DB::select('select count(*) as existe from faturas where num_doc = '. $numChk);
            if($ok[0]->existe > 0){
                $numdoc = time() + rand();
                $fnCheck($numdoc);
            }
        };
        $fnCheck($numdoc);
        return (string)$numdoc;
    }

    public function getTotalBoletoAttribute()
    {
        $data = $this->attributes['total'] + 1.97;
        return $data;
    }

    public function getTotalCartaoCreditoAttribute()
    {
        $data = $this->attributes['total'] + ($this->attributes['total'] * ( 2.80 / 100 ));
        return $data;
    }

    public function getTotalCartaoDebitoAttribute()
    {
        $data = $this->attributes['total'] + ($this->attributes['total'] * ( 2.40 / 100 ));
        return $data;
    }
}
