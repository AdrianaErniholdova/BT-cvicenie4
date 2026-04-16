<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->premium_until || $user->premium_until < now()) {
            return response()->json([
                'message' => 'Táto funkcia je dostupná iba pre prémiových používateľov.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
