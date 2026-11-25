<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /** Usage : ->middleware('role:admin,vendeur'). Mauvais rôle = 404 (on ne révèle pas que la page existe). */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $role = $user->isClient() ? 'client' : $user->role;

        if (! in_array($role, $roles, true)) {
            abort(404);
        }

        return $next($request);
    }
}
