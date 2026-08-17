<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaStatus extends Model
{
    use HasFactory;

    protected $table = 'fatura_status';
    
    protected $fillable = [
        'nome'
    ];
}
