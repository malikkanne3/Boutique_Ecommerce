<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Notifier;
use App\Support\Portal;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'        => ['nullable', 'string', 'max:30'],
            'account_type' => ['required', 'in:client,vendeur'],
            'shop_name'    => ['required_if:account_type,vendeur', 'nullable', 'string', 'max:120'],
            'photo'        => ['nullable', 'image', 'max:2048'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $isVendeur = $request->account_type === 'vendeur';
        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store($isVendeur ? 'shops' : 'avatars', 'public') : null;

        // Seuls "client" et "vendeur" peuvent être choisis ; un vendeur doit d'abord être validé par un admin
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'role'      => $request->account_type,
            'shop_name' => $isVendeur ? $request->shop_name : null,
            'status'    => $isVendeur ? 'pending' : 'active',
            'shop_logo' => $isVendeur ? $photoPath : null,
            'avatar'    => $isVendeur ? null : $photoPath,
            'password'  => Hash::make($request->password),
        ]);

        event(new Registered($user));

        if ($isVendeur) {
            foreach (User::where('role', 'admin')->where('status', 'active')->pluck('id') as $adminId) {
                Notifier::notify($adminId, '🏪 Nouvelle demande vendeur', "{$user->name} ({$user->shop_name}) demande l'ouverture d'une boutique.", 'shop', 'warning', Portal::path('admin', 'users?status=pending'));
            }
            Notifier::mail($user->email, $user->name, 'Votre demande de boutique est bien reçue',
                '<h2>Merci ' . e($user->name) . ' !</h2><p>Votre demande pour la boutique <strong>' . e($user->shop_name) . '</strong> est en cours de validation par notre équipe.</p><p>Vous recevrez un e-mail avec votre lien de connexion dès qu\'elle sera acceptée.</p>');

            return redirect()->route('login')->with('success', 'Demande envoyée ! Un administrateur va valider votre boutique. Vous recevrez un e-mail avec votre lien de connexion.');
        }

        Auth::login($user);

        return redirect($user->dashboardUrl())->with('success', 'Bienvenue sur E-Shop SN, ' . $user->name . ' !');
    }
}
