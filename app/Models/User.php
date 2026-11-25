<?php
namespace App\Models;

use App\Services\Notifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'client'  => 'Client',
        'vendeur' => 'Vendeur',
        'livreur' => 'Livreur',
        'admin'   => 'Administrateur',
    ];

    public const STATUSES = [
        'active'    => 'Actif',
        'pending'   => 'À valider',
        'suspended' => 'Désactivé',
        'rejected'  => 'Refusé',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'shop_name', 'address',
        'status', 'created_by', 'must_change_password',
        'avatar', 'shop_logo', 'address_lat', 'address_lng',
        'is_available', 'last_lat', 'last_lng', 'last_seen_at',
        'shop_banner', 'shop_description', 'shop_hours', 'vacation_mode', 'vacation_until', 'vacation_message',
    ];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
            'is_available'         => 'boolean',
            'vacation_mode'        => 'boolean',
            'vacation_until'       => 'date',
            'last_seen_at'         => 'datetime',
            'shop_hours'           => 'array',
        ];
    }

    protected static function booted(): void
    {
        // Un vendeur supprimé ne laisse pas de produits/commandes orphelins
        static::deleting(function (User $user) {
            Product::withTrashed()->where('seller_id', $user->id)->update(['seller_id' => null]);
            Order::where('seller_id', $user->id)->update(['seller_id' => null]);
        });
    }

    /* ---------- Rôles ---------- */
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isVendeur(): bool { return $this->role === 'vendeur'; }
    public function isLivreur(): bool { return $this->role === 'livreur'; }
    public function isClient(): bool  { return ! in_array($this->role, ['admin', 'vendeur', 'livreur'], true); }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'   => 'admin.dashboard',
            'vendeur' => 'vendeur.dashboard',
            'livreur' => 'livreur.dashboard',
            default   => 'client.dashboard',
        };
    }

    public function dashboardUrl(): string
    {
        return route($this->dashboardRoute());
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Client';
    }

    /* ---------- Statut du compte ---------- */
    public function getAccountStatusAttribute(): string
    {
        return $this->status ?? 'active';
    }

    public function isActiveAccount(): bool
    {
        return $this->account_status === 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->account_status] ?? 'Actif';
    }

    /** Un admin ne peut gérer ni lui-même, ni un admin qu'il n'a pas créé. */
    public function canBeManagedBy(User $admin): bool
    {
        if ($this->id === $admin->id) {
            return false;
        }
        if ($this->isAdmin()) {
            return (int) $this->created_by === (int) $admin->id;
        }
        return true;
    }

    /** Photo à afficher : photo perso, sinon logo de la boutique (vendeur). */
    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->avatar ?: ($this->isVendeur() ? $this->shop_logo : null);
        return $path ? asset('storage/' . $path) : null;
    }

    /* ---------- Page boutique ---------- */
    public const DAYS = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];

    public function getShopBannerUrlAttribute(): ?string
    {
        return $this->shop_banner ? asset('storage/' . $this->shop_banner) : null;
    }

    public function getShopLogoUrlAttribute(): ?string
    {
        return $this->shop_logo ? asset('storage/' . $this->shop_logo) : null;
    }

    public function getShopUrlAttribute(): string
    {
        return route('shops.show', $this);
    }

    /** Horaires par jour (1 = lundi … 7 = dimanche) avec valeurs par défaut. */
    public function hoursForDay(int $day): array
    {
        $h = ($this->shop_hours ?? [])[$day] ?? null;
        return [
            'closed' => (bool) ($h['closed'] ?? false),
            'open'   => $h['open'] ?? '08:00',
            'close'  => $h['close'] ?? '19:00',
        ];
    }

    public function getHasHoursAttribute(): bool
    {
        return ! empty($this->shop_hours);
    }

    /** Boutique ouverte maintenant ? (null = horaires non renseignés). */
    public function isOpenNow(): ?bool
    {
        if (! $this->has_hours) {
            return null;
        }
        $h = $this->hoursForDay((int) now()->dayOfWeekIso);
        if ($h['closed']) {
            return false;
        }
        $t = now()->format('H:i');
        return $t >= $h['open'] && $t <= $h['close'];
    }

    public function getOnVacationAttribute(): bool
    {
        if (! $this->vacation_mode) {
            return false;
        }
        return ! ($this->vacation_until && $this->vacation_until->lt(today()));
    }

    public function getInitialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name ?? '?', 0, 1));
    }

    public function getDisplayNameAttribute(): string
    {
        return ($this->isVendeur() && $this->shop_name) ? $this->shop_name : $this->name;
    }

    /* ---------- Relations ---------- */
    public function orders()       { return $this->hasMany(Order::class); }
    public function products()     { return $this->hasMany(Product::class, 'seller_id'); }
    public function sellerOrders() { return $this->hasMany(Order::class, 'seller_id'); }
    public function deliveries()   { return $this->hasMany(Order::class, 'livreur_id'); }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function sentMessages()     { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages() { return $this->hasMany(Message::class, 'receiver_id'); }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_members', 'user_id', 'group_id')
            ->withPivot('last_read_at')->withTimestamps();
    }

    protected ?int $unreadMessagesMemo = null;

    /** Messages directs non lus + messages de groupes non lus. */
    public function unreadMessagesCount(): int
    {
        if ($this->unreadMessagesMemo !== null) {
            return $this->unreadMessagesMemo;
        }

        $count = $this->receivedMessages()->where('is_read', false)->count();
        foreach ($this->chatGroups()->get() as $group) {
            $count += $group->unreadFor($this);
        }

        return $this->unreadMessagesMemo = $count;
    }

    /* ---------- Mot de passe oublié (envoyé par l'API Brevo) ---------- */
    public function sendPasswordResetNotification($token): void
    {
        $url = route('password.reset', ['token' => $token, 'email' => $this->email]);

        Notifier::mail($this->email, $this->name, 'Réinitialisation de votre mot de passe',
            '<h2>Mot de passe oublié ?</h2><p>Bonjour ' . e($this->name) . ', cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe. Ce lien expire dans 60 minutes.</p>'
            . '<p style="text-align:center;margin:26px 0"><a href="' . e($url) . '" style="background:#7c3aed;color:#fff;padding:13px 26px;border-radius:10px;text-decoration:none;font-weight:bold">Choisir un nouveau mot de passe</a></p>'
            . '<p style="font-size:12px;color:#888">Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement ce message.</p>');
    }

    /* ---------- Messagerie : uniquement entre personnes liées par une commande ----------
     * client  <-> vendeur : le client a (ou a eu) une commande chez ce vendeur
     * client  <-> livreur : tant que sa commande est assignée à CE livreur (livraison en cours)
     * vendeur <-> livreur : le livreur est (ou a été) assigné à une commande du vendeur
     * vendeur <-> vendeur : un même panier d'un client concerne leurs deux boutiques
     * admin   : aucune discussion (il envoie des annonces en lecture seule)
     */
    protected ?array $linkedIdsMemo = null;

    /** Identifiants des utilisateurs avec lesquels je peux discuter. */
    public function linkedUserIds(): array
    {
        if ($this->linkedIdsMemo !== null) {
            return $this->linkedIdsMemo;
        }
        if ($this->isAdmin()) {
            return $this->linkedIdsMemo = [];
        }

        $inDelivery = ['assigned', 'delivering', 'problem'];
        $ids = collect();

        if ($this->isLivreur()) {
            $ids = $ids->merge(Order::where('livreur_id', $this->id)->whereNotNull('seller_id')->pluck('seller_id'))   // vendeurs de ses livraisons
                ->merge(Order::where('livreur_id', $this->id)->whereIn('status', $inDelivery)->pluck('user_id'));       // clients en cours de livraison
        } elseif ($this->isVendeur()) {
            $refs = Order::where('seller_id', $this->id)->whereNotNull('checkout_ref')->pluck('checkout_ref')->unique();
            $ids = $ids->merge(Order::where('seller_id', $this->id)->pluck('user_id'))                                  // ses clients
                ->merge(Order::where('seller_id', $this->id)->whereNotNull('livreur_id')->pluck('livreur_id'))         // livreurs de ses commandes
                ->merge($refs->isEmpty() ? [] : Order::whereIn('checkout_ref', $refs)->whereNotNull('seller_id')
                    ->where('seller_id', '!=', $this->id)->pluck('seller_id'));                                        // vendeurs d'un même panier
        } else {
            $ids = $ids->merge(Order::where('user_id', $this->id)->whereNotNull('seller_id')->pluck('seller_id'))      // vendeurs de ses commandes
                ->merge(Order::where('user_id', $this->id)->whereIn('status', $inDelivery)->whereNotNull('livreur_id')->pluck('livreur_id')); // son livreur
        }

        return $this->linkedIdsMemo = $ids->filter()->map(fn ($i) => (int) $i)->reject(fn ($i) => $i === (int) $this->id)->unique()->values()->all();
    }

    public function contactableUsers()
    {
        $ids = $this->linkedUserIds();
        if (empty($ids)) {
            return collect();
        }
        return User::whereIn('id', $ids)->where('status', 'active')->orderBy('name')->get();
    }

    public function canMessage(User $other): bool
    {
        if ($other->id === $this->id || $this->isAdmin() || $other->isAdmin()) {
            return false;
        }
        if ($other->account_status !== 'active') {
            return false;
        }
        return in_array((int) $other->id, $this->linkedUserIds(), true);
    }
}
