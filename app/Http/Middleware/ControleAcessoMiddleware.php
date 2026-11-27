<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ControleAcessoMiddleware
{
    protected $auth;
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // As verificações precisam acontecer ANTES de $next($request):
        // caso contrário a ação (ex.: exclusão) é executada e só depois redirecionada.
        if ($this->auth->guest()) {
            return redirect()->guest('login');
        }

        $chk1 = $request->user()->is_admin;
        $chk2 = ($request->is('c/*') ? true : false);
        $chk3 = $request->segment(1) =='c';

        if($request->segment(2) === 'autenticar' && $request->is('a/*'))
        {
            return $next($request);
        }

        if ($chk1 == 0) {
            if (!$chk2 && !$chk3) {
                return redirect()->guest('login');
            }
        }

        return $next($request);
    }
}