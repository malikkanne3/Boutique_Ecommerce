<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Déconnecte immédiatement un compte désactivé, refusé ou pas encore validé. */
class EnsureAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $status = $user ? ($user->status ?? 'active') : 'active';

        if ($user && $status !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $messages = [
                'pending'   => 'Votre demande de compte vendeur est en cours de validation par l\'administration.',
                'rejected'  => 'Votre demande de compte a été refusée.',
                'suspended' => 'Ce compte a été désactivé. Contactez l\'administration.',
            ];

            return redirect()->route('login')->withErrors(['email' => $messages[$status] ?? $messages['suspended']]);
        }

        return $next($request);
    }
}
