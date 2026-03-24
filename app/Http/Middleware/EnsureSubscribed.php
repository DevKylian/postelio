<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->canPublishToSocialMedia()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Abonnement premium requis.'], 403);
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'Un abonnement premium est requis pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
