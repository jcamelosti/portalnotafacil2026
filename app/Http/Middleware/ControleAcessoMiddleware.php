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
        //return $next($request);
        $response = $next($request);

        if (!$this->auth->guest()) {
            $chk1 = $request->user()->is_admin;
            $chk2 = ($request->is('c/*') ? true : false);
            $chk3 = $request->segment(1) =='c';

            if($request->segment(2) === 'autenticar' && $request->is('a/*'))
            {
                return $response;
            }

            if ($chk1 == 0) {
                if (!$chk2 && !$chk3) {
                    return redirect()->guest('login');
                }
            }
            
            return $response;
        } else {
            return redirect()->guest('login');
        }
    }
}