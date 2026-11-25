<?php
namespace App\Http\Controllers;

use App\Models\User;

/** Page de connexion dédiée à chaque rôle, accessible uniquement par son lien secret. */
class PortalController extends Controller
{
    private const META = [
        'admin' => [
            'icon'   => 'shield-lock-fill',
            'title'  => 'Espace Administrateur',
            'tag'    => 'Supervisez toute la plateforme.',
            'points' => ['Utilisateurs, vendeurs et livreurs', 'Catalogue et catégories', 'Toutes les commandes et statistiques'],
        ],
        'vendeur' => [
            'icon'   => 'shop-window',
            'title'  => 'Espace Vendeur',
            'tag'    => 'Gérez votre boutique au quotidien.',
            'points' => ['Vos produits et vos stocks', 'Vos commandes et vos livreurs', 'Vos ventes en un coup d\'œil'],
        ],
        'livreur' => [
            'icon'   => 'truck',
            'title'  => 'Espace Livreur',
            'tag'    => 'Vos missions, simplement.',
            'points' => ['Missions à accepter', 'Itinéraire et appel du client', 'Historique de vos livraisons'],
        ],
    ];

    public function show(string $role)
    {
        abort_unless(isset(self::META[$role]), 404);

        // Déjà connecté : direction son propre tableau de bord
        if (auth()->check()) {
            return redirect(auth()->user()->dashboardUrl());
        }

        return view('auth.portal', [
            'role'  => $role,
            'meta'  => self::META[$role],
            'label' => User::ROLES[$role],
        ]);
    }
}
