<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $maxAge = 86400; 

        return $response->header('Cache-Control', "public, max-age={$maxAge}")
                        ->header('Pragma', 'cache')
                        ->header('Expires', now()->addSeconds($maxAge)->toRfc7231String());
    }
}
