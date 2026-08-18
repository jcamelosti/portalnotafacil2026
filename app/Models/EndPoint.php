<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndPoint extends Model
{
    use HasFactory;

    protected $table = 'endpoints';
    
    protected $fillable = [
        'codigo_municipio',
        'url_endpoint',
    ];
}
