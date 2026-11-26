<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseLog extends Model
{
    protected $table = 'license_logs';

    protected $fillable = [
        'license_id',
        'empresa_id',
        'user_id',
        'validate_anterior',
        'validate_nova',
        'plano_anterior_id',
        'plano_novo_id',
        'observacao',
    ];

    protected $casts = [
        'validate_anterior' => 'date',
        'validate_nova' => 'date',
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planoAnterior()
    {
        return $this->belongsTo(Plano::class, 'plano_anterior_id');
    }

    public function planoNovo()
    {
        return $this->belongsTo(Plano::class, 'plano_novo_id');
    }
}
