<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Maps a verified proxy identity to an existing account without changing roles, passwords or owned data. */
class AutheliaIdentity
{
    /** Checks the request's secret and fixed user binding, then calls $next with an authenticated session. */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('MONITOR_SSO_ENABLED') !== '1') {
            return $next($request);
        }
        $secret = (string) env('MONITOR_SSO_SECRET', '');
        $mapping = json_decode((string) env('MONITOR_SSO_USERS', '{}'), true);
        $username = (string) $request->header('X-Monitor-User', '');
        abort_unless(strlen($secret) >= 32 && hash_equals($secret, (string) $request->header('X-Monitor-Secret', '')), 403);
        abort_unless(is_array($mapping) && isset($mapping[$username]) && is_int($mapping[$username]), 403);
        $user = User::find($mapping[$username]);
        abort_unless($user !== null, 403);
        if (Auth::id() !== $user->id) {
            Auth::login($user, false);
            $request->session()->regenerate();
        }

        return $next($request);
    }
}
