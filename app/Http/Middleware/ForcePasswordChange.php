<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Oblige à choisir un vrai mot de passe après une connexion avec un mot de passe provisoire. */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ($user->must_change_password ?? false)
            && ! $request->routeIs('password.force', 'password.force.update', 'logout')) {
            return redirect()->route('password.force');
        }

        return $next($request);
    }
}
