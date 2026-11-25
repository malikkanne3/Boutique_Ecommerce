<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Portal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user     = auth()->user();
        $portal   = $request->input('portal');
        $expected = in_array($portal, ['admin', 'vendeur', 'livreur'], true) ? $portal : 'client';

        // Chaque page de connexion n'accepte que les comptes de son propre espace
        $roleOk = $expected === 'client' ? $user->isClient() : $user->role === $expected;
        if (! $roleOk) {
            $this->forget($request);
            return redirect($expected === 'client' ? route('login') : Portal::url($expected))
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte de cet espace.']);
        }

        // Compte pas encore validé / désactivé / refusé
        $status = $user->status ?? 'active';
        if ($status !== 'active') {
            $messages = [
                'pending'   => 'Votre demande de compte vendeur est en cours de validation par l\'administration. Vous recevrez un e-mail dès qu\'elle sera traitée.',
                'rejected'  => 'Votre demande de compte a été refusée.',
                'suspended' => 'Ce compte a été désactivé. Contactez l\'administration.',
            ];
            $this->forget($request);
            return redirect($expected === 'client' ? route('login') : Portal::url($expected))
                ->withInput($request->only('email'))
                ->withErrors(['email' => $messages[$status] ?? $messages['suspended']]);
        }

        return redirect()->intended($user->dashboardUrl());
    }

    public function destroy(Request $request): RedirectResponse
    {
        $role = $request->user() ? $request->user()->role : null;
        $this->forget($request);

        return redirect(in_array($role, ['admin', 'vendeur', 'livreur'], true) ? Portal::url($role) : '/');
    }

    private function forget(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
