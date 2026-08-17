<?php

namespace App\Exceptions;

use App\Mail\ErroSistemaMail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //

        //capturando erros
        $this->reportable(function (Throwable $e) {

            // Evitar enviar em ambiente local
            if (app()->environment('local')) {
                return;
            }

            try {
                $user = Auth::user();

                $dados = [
                    'mensagem' => $e->getMessage(),
                    'arquivo' => $e->getFile(),
                    'linha' => $e->getLine(),
                    'url' => request()->fullUrl(),
                    'metodo' => request()->method(),
                    'ip' => request()->ip(),
                    'user' => $user ? [
                        'id' => $user->id,
                        'nome' => $user->name ?? null,
                        'email' => $user->email ?? null,
                    ] : null,
                    'trace' => $e->getTraceAsString(),
                ];

                /*Mail::raw(json_encode($dados, JSON_PRETTY_PRINT), function ($message) {
                    $message->to('seuemail@dominio.com')
                            ->subject('🚨 Erro no sistema Laravel');
                });*/

                Mail::to('josueprg@gmail.com')
                    ->send(new ErroSistemaMail($dados));

            } catch (\Exception $mailException) {
                // Evita loop infinito caso o email falhe
            }
        });
    }
}
