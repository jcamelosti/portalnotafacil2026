<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacaoGatewayPagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'metodo_origem',
        'transacao_id',
        'conteudo_notificacao',
        'processado'
    ];
}
