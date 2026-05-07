<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\BrevoMail;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $query = User::withCount('orders')->latest();
        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.request('search').'%')
                  ->orWhere('email', 'like', '%'.request('search').'%');
            });
        }
        if (request('role')) {
            $query->where('role', request('role'));
        }
        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('orders');
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store()
    {
        $data = request()->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'zone'  => 'nullable|string|max:255',
        ]);

        $plainPassword = Str::random(10);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($plainPassword),
            'role'     => 'livreur',
        ]);

        BrevoMail::send(
    $user->email,
    $user->name,
    '🚀 Bienvenue chez E-Shop SN — Vos identifiants',
    view('emails.livreur-bienvenue', ['user' => $user, 'plainPassword' => $plainPassword])->render()
);

        return redirect()->route('admin.users.index')
            ->with('success', "Compte livreur créé ! Email envoyé à {$user->email}");
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Impossible de supprimer un admin !');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}