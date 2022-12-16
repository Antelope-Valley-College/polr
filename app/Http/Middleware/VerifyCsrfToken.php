<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    public function handle($request, \Closure $next) {
        if ($request->is('api/v*/action/*') || $request->is('api/v*/data/*')) {
            // Exclude public API from CSRF protection
            // but do not exclude private API endpoints
            return $next($request);
        }

        if (in_array('SAML', explode(',', env('POLR_LOGIN_MODES'))) && $request->is('saml/sp/*')) {
            // Exclude SAML pages from CSRF protection
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
