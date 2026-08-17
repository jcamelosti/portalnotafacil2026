<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin', 'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function cliente(){
        return $this->hasOne(Cliente::class, 'user_id', 'id');
    }

    public function list(){
        return ['' =>'Selecione o Usuário Responsável'] + $this
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->all();
    }


    public function listEmpresasCountByUser()
    {
        return ['' => 'Selecione o Usuário Responsável'] + $this
            /*->leftJoin('empresas', 'empresas.user_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, COUNT(empresas.id) as total_empresas')
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name', 'asc')*/
            ->leftJoin('empresas', 'empresas.user_id', '=', 'users.id')
            ->leftJoin('empresas_compartilhadas', function ($join) {
                $join->on('empresas_compartilhadas.solicitante_user_id', '=', 'users.id');
            })
            ->selectRaw("
                users.id,
                users.name,
                COUNT(DISTINCT COALESCE(empresas.id, empresas_compartilhadas.empresa_id)) as total_empresas
            ")
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name', 'asc')
            ->get()
            ->mapWithKeys(function ($user) {
                return [
                    $user->id => "{$user->name} ({$user->total_empresas})"
                ];
            })
            ->toArray();
    }

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    public function creditosUser(){
        return $this->hasOne(CreditoUser::class);
    }

    public function empresasCompartilhadas(){
        return $this->hasMany(EmpresaCompartilhada::class, 'solicitante_user_id', 'id');
    }
}
