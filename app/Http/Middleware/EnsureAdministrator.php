<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->role?->name === 'Administrator', 403);

        $resource = $request->route('resource');
        abort_if($resource && ! in_array($resource, ['users', 'bidang', 'desa', 'kecamatan'], true), 403);

        return $next($request);
    }
}
