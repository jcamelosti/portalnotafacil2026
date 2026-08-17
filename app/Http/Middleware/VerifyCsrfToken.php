<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'retorno/mercadopago',
        'webhook/mercadopago/capture',
        'webhook/infinitepay/capture',
        'api/*',
    ];
}
