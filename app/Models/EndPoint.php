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

    public function getUrlEndPointAttribute(){
        $url = $this->attributes['url_endpoint'];

        if(getenv('VERSAO_WEB_SERVICE') == '2.04'){
           $url = $this->attributes['url_endpoint2'];
        }
        
        return $url;
    }
}
