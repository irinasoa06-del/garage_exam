<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        // Nettoyer tout output buffer existant (espaces, warnings, etc.) qui casserait le JSON
        if (ob_get_length()) {
            ob_clean();
        }
        
        // Force JSON content type for API responses
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
