<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrelacaoTribNacNbs extends Model
{
    use HasFactory;

    protected $table = 'correlacoes_trib_nac_nbs';

    protected $fillable = [
        'c_trib_nac',
        'x_trib_nac',
        'c_nbs',
        'x_nbs',
        'c_class_trib',
        'x_class_trib',
        'cst',
        'x_st',
        'c_ind_op',
        'x_ind_op',
        'ps_onerosa',
        'adq_exterior',
        'local_incidencia_ibs',
    ];
}
