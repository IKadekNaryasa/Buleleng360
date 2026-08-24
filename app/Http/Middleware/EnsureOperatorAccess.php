<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperatorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->role?->name === 'Operator', 403);

        $resource = $request->route('resource');
        abort_if($resource && ! in_array($resource, ['partai', 'ormas', 'penduduk', 'agama', 'sebaran-agama'], true), 403);

        return $next($request);
    }
}
