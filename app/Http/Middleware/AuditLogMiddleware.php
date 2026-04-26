<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // Exclude noisy requests
        if ($this->shouldExclude($request)) {
            return $response;
        }

        try {
            $user = Auth::user();
            $ip = $request->ip() === '127.0.0.1' ? '8.8.8.8' : $request->ip(); // Mock IP for local testing
            
            $location = \Illuminate\Support\Facades\Cache::remember('ip_location_' . $ip, now()->addDay(), function () use ($ip) {
                return Location::get($ip);
            });

            AuditLog::create([
                'user_id'       => $user?->id,
                'email'         => $user?->email,
                'ip'            => $request->ip(),
                'method'        => $request->method(),
                'url'           => $this->getCleanUrl($request),
                'tag'           => $this->determineTag($request),
                'city'          => $location?->cityName,
                'country'       => $location?->countryName,
                'rank'          => $user?->getRoleKey() ?? 'guest',
                'user_agent'    => $request->userAgent(),
                'payload'       => $this->getCleanPayload($request),
                'response_time' => (int) ((microtime(true) - $startTime) * 1000),
            ]);
        } catch (\Exception $e) {
            // Never block the user request if logging fails
            \Illuminate\Support\Facades\Log::error('Audit Log failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    protected function shouldExclude(Request $request): bool
    {
        // Don't log Livewire internal requests (updates, polling, etc)
        if ($request->hasHeader('X-Livewire')) {
            return true;
        }

        $path = $request->path();
        
        $excludedPaths = [
            'livewire/*',
            '_debugbar/*',
            'telescope/*',
            'api/webhooks/*', // Webhooks are logged separately usually, but optional
        ];

        foreach ($excludedPaths as $excluded) {
            if ($request->is($excluded) || str_contains($request->getRequestUri(), 'livewire')) {
                return true;
            }
        }

        // Also exclude static common extensions
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico)$/i', $path)) {
            return true;
        }

        return false;
    }

    protected function determineTag(Request $request): string
    {
        $path = $request->path();
        
        if ($request->is('admin/*')) return 'admin';
        if ($request->is('login') || $request->is('register')) return 'auth';
        if ($request->is('generate-post/*') || $request->is('posts')) return 'posts';
        if ($request->is('api/*')) return 'api';
        
        return 'navigation';
    }

    protected function getCleanPayload(Request $request): ?array
    {
        $payload = $request->except(['password', 'password_confirmation', '_token', 'card_number', 'cvc']);
        return empty($payload) ? null : $payload;
    }

    protected function getCleanUrl(Request $request): string
    {
        $url = $request->fullUrl();
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'cvc', 'card_number'];

        foreach ($sensitiveKeys as $key) {
            if ($request->has($key)) {
                $url = preg_replace('/' . $key . '=[^&]*/', $key . '=[REDACTED]', $url);
            }
        }

        return $url;
    }
}
