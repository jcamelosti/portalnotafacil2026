<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoUser extends Model
{
    use HasFactory;

    protected $table = 'credito_users';
    
    protected $fillable = [
        'user_id',
        'credito'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getCreditoFmtAttribute($value){
        return number_format( $this->attributes['credito'], 2, ',', '.');
    }

    public function incluirIfNotExists($user_id){
        $credito = $this->where('user_id', $user_id)->first();
        if(!$credito){
            $this->create([
                'user_id' => $user_id,
                'credito' => 0.00
            ]);
        }
    }
}
