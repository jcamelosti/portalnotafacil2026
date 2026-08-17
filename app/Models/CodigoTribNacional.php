<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CodigoTribNacional extends Model
{
    use HasFactory;

    protected $table = 'cod_tributacao_nacional';
      
    protected $fillable = [
       'codigo',
       'descricao'
    ];
}
