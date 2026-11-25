<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Notifier;
use App\Support\Portal;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    /** Réponse d'erreur si l'admin n'a pas le droit de gérer ce compte (lui-même, ou un admin qu'il n'a pas créé). */
    private function denied(User $target)
    {
        if ($target->canBeManagedBy(auth()->user())) {
            return null;
        }
        return back()->with('error', "Vous ne pouvez pas gérer ce compte : il s'agit de votre propre compte ou d'un administrateur que vous n'avez pas créé.");
    }

    private function loginLink(User $user): string
    {
        return $user->isClient() ? route('login') : Portal::url($user->role);
    }

    public function index()
    {
        $query = User::withCount('orders')->latest();

        if ($s = request('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%')
                  ->orWhere('email', 'like', '%' . $s . '%')
                  ->orWhere('shop_name', 'like', '%' . $s . '%');
            });
        }
        if ($role = request('role')) {
            if ($role === 'client') {
                $query->whereNotIn('role', ['admin', 'vendeur', 'livreur']);
            } else {
                $query->where('role', $role);
            }
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(15)->withQueryString();

        $counts = User::selectRaw('role, COUNT(*) as c')->groupBy('role')->pluck('c', 'role');
        $roleCounts = [
            'all'     => $counts->sum(),
            'client'  => $counts->get('client', 0),
            'vendeur' => $counts->get('vendeur', 0),
            'livreur' => $counts->get('livreur', 0),
            'admin'   => $counts->get('admin', 0),
        ];
        $pendingCount = User::where('status', 'pending')->count();

        return view('admin.users.index', compact('users', 'roleCounts', 'pendingCount'));
    }

    public function show(User $user)
    {
        $orders = Order::with('products')->visibleTo($user)->latest()->take(8)->get();
        $productsCount = $user->isVendeur() ? Product::where('seller_id', $user->id)->count() : null;
        $revenue = $user->isVendeur()
            ? Order::where('seller_id', $user->id)->where('status', '!=', 'cancelled')->sum('total_amount')
            : null;
        $creator = $user->created_by ? User::find($user->created_by) : null;

        return view('admin.users.show', compact('user', 'orders', 'productsCount', 'revenue', 'creator'));
    }

    public function create()
    {
        return view('admin.users.create', ['preRole' => request('role', 'vendeur')]);
    }

    public function store()
    {
        $data = request()->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:30',
            'role'          => 'required|in:client,vendeur,livreur,admin',
            'shop_name'     => 'required_if:role,vendeur|nullable|string|max:120',
            'password_mode' => 'required|in:generate,manual',
            'password'      => 'required_if:password_mode,manual|nullable|string|min:8|max:100',
        ]);

        // Mot de passe provisoire : généré, ou saisi par l'admin. L'utilisateur devra le changer à sa première connexion.
        $plain = $data['password_mode'] === 'manual' ? $data['password'] : Str::random(10);

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'phone'                => $data['phone'] ?? null,
            'role'                 => $data['role'],
            'shop_name'            => $data['role'] === 'vendeur' ? ($data['shop_name'] ?? null) : null,
            'status'               => 'active',
            'created_by'           => auth()->id(),
            'must_change_password' => true,
            'password'             => $plain,
        ]);

        $link = $this->loginLink($user);
        Notifier::mail($user->email, $user->name, '🚀 Bienvenue chez E-Shop SN — vos identifiants',
            '<h2>Bienvenue ' . e($user->name) . ' !</h2><p>Un compte <strong>' . $user->role_label . '</strong> a été créé pour vous.</p>'
            . '<p>Email : <strong>' . e($user->email) . '</strong><br>Mot de passe provisoire : <strong>' . e($plain) . '</strong></p>'
            . '<p style="text-align:center;margin:22px 0"><a href="' . e($link) . '" style="background:#7c3aed;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold">Me connecter</a></p>'
            . '<p style="font-size:13px;color:#666">Vous devrez choisir votre propre mot de passe dès la première connexion. Gardez ce lien de connexion pour vous.</p>');

        return redirect()->route('admin.users.index')
            ->with('success', "Compte {$user->role_label} créé pour {$user->name}.")
            ->with('generated_password', ['email' => $user->email, 'password' => $plain, 'link' => $link]);
    }

    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'Ce compte n\'est pas en attente de validation.');
        }
        $user->update(['status' => 'active']);

        Notifier::mail($user->email, $user->name, '✅ Votre boutique est validée',
            '<h2>Félicitations ' . e($user->name) . ' !</h2><p>Votre boutique <strong>' . e($user->shop_name) . '</strong> a été validée. Vous pouvez dès maintenant vous connecter et publier vos produits.</p>'
            . '<p style="text-align:center;margin:22px 0"><a href="' . e($this->loginLink($user)) . '" style="background:#059669;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold">Accéder à mon espace vendeur</a></p>'
            . '<p style="font-size:13px;color:#666">Conservez précieusement ce lien : c\'est votre page de connexion personnelle.</p>');

        return back()->with('success', "La boutique de {$user->name} est validée. Un e-mail avec son lien de connexion lui a été envoyé.");
    }

    public function reject(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'Ce compte n\'est pas en attente de validation.');
        }
        $user->update(['status' => 'rejected']);

        Notifier::mail($user->email, $user->name, 'Votre demande de boutique',
            '<h2>Bonjour ' . e($user->name) . '</h2><p>Après examen, votre demande d\'ouverture de boutique n\'a malheureusement pas pu être acceptée pour le moment.</p><p>Vous pouvez contacter l\'administration pour plus d\'informations.</p>');

        return back()->with('success', 'Demande refusée.');
    }

    public function toggleStatus(User $user)
    {
        if ($r = $this->denied($user)) {
            return $r;
        }
        if (! in_array($user->account_status, ['active', 'suspended'], true)) {
            return back()->with('error', 'Utilisez « Valider » ou « Refuser » pour une demande en attente.');
        }

        $suspend = $user->account_status === 'active';
        $user->update(['status' => $suspend ? 'suspended' : 'active']);

        return back()->with('success', $suspend
            ? "Le compte de {$user->name} est désactivé. Il est déconnecté immédiatement."
            : "Le compte de {$user->name} est réactivé.");
    }

    public function resetPassword(User $user)
    {
        if ($r = $this->denied($user)) {
            return $r;
        }

        $plain = Str::random(10);
        $user->update(['password' => $plain, 'must_change_password' => true]);

        Notifier::mail($user->email, $user->name, '🔑 Votre mot de passe a été réinitialisé',
            '<h2>Mot de passe réinitialisé</h2><p>Un administrateur a réinitialisé votre mot de passe.</p><p>Mot de passe provisoire : <strong>' . e($plain) . '</strong></p>'
            . '<p style="text-align:center;margin:22px 0"><a href="' . e($this->loginLink($user)) . '" style="background:#7c3aed;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold">Me connecter</a></p>'
            . '<p style="font-size:13px;color:#666">Vous devrez en choisir un nouveau dès la connexion.</p>');

        return back()->with('success', "Mot de passe de {$user->name} réinitialisé.")
            ->with('generated_password', ['email' => $user->email, 'password' => $plain, 'link' => $this->loginLink($user)]);
    }

    public function destroy(User $user)
    {
        if ($r = $this->denied($user)) {
            return $r;
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Compte supprimé. Son adresse e-mail est de nouveau disponible.');
    }
}
