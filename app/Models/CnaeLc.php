<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CnaeLc extends Model
{
    protected $table = 'cnae_lc';

    protected $fillable = [
        'cnae_mascara',
        'cnae', 
        'descricao_cnae',
        'item_lc',
        'descricao_item',
    ];
}
