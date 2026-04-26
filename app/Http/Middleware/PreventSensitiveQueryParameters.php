<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSensitiveQueryParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define parameters that should NEVER be in the query string (GET parameters)
        $sensitiveKeys = ['password', 'password_confirmation', 'cvc', 'card_number'];

        foreach ($sensitiveKeys as $key) {
            if ($request->query->has($key)) {
                // Log the suspicious attempt (without the value)
                \Illuminate\Support\Facades\Log::warning('Suspicious request: sensitive data in query string', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                    'parameter' => $key
                ]);

                // Abort with a 400 Bad Request
                abort(Response::HTTP_BAD_REQUEST, "Sensitive data is not allowed in the query string.");
            }
        }

        return $next($request);
    }
}
