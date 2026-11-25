<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules;

/** Changement obligatoire du mot de passe provisoire. */
class ForcePasswordController extends Controller
{
    public function show()
    {
        if (! (auth()->user()->must_change_password ?? false)) {
            return redirect(auth()->user()->dashboardUrl());
        }
        return view('auth.force-password');
    }

    public function update()
    {
        $data = request()->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = auth()->user();

        if (\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['password' => 'Choisissez un mot de passe différent du mot de passe provisoire.']);
        }

        $user->update(['password' => $data['password'], 'must_change_password' => false]);

        return redirect($user->dashboardUrl())->with('success', 'Mot de passe enregistré. Bienvenue !');
    }
}
