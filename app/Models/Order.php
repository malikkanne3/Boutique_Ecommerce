<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'seller_id', 'order_number', 'total_amount', 'status',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'livreur_id', 'delivered_at',
        'payment_method', 'payment_status', 'delivery_lat', 'delivery_lng', 'delivery_note',
        'courier_lat', 'courier_lng', 'courier_updated_at', 'delivery_code', 'proof_path',
        'incident_type', 'incident_note', 'incident_at', 'courier_fee', 'seller_note', 'checkout_ref',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
        'courier_updated_at' => 'datetime',
        'incident_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending'    => 'En attente',
        'confirmed'  => 'Confirmée',
        'assigned'   => 'Livreur assigné',
        'delivering' => 'En livraison',
        'delivered'  => 'Livrée',
        'problem'    => 'Problème de livraison',
        'cancelled'  => 'Annulée',
    ];

    /** Étapes affichées dans la frise de suivi */
    public const FLOW = ['pending', 'confirmed', 'assigned', 'delivering', 'delivered'];

    public const ACTIVE = ['pending', 'confirmed', 'assigned', 'delivering', 'problem'];

    public const INCIDENTS = [
        'client_unreachable' => 'Client injoignable',
        'address_not_found'  => 'Adresse introuvable',
        'client_absent'      => 'Client absent à la livraison',
        'refused'            => 'Colis refusé par le client',
        'other'              => 'Autre problème',
    ];

    public function disputes() { return $this->hasMany(Dispute::class); }

    /** Autres commandes du même panier (chez d'autres vendeurs). */
    public function siblings()
    {
        return Order::where('checkout_ref', $this->checkout_ref)->where('id', '!=', $this->id);
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function seller()  { return $this->belongsTo(User::class, 'seller_id'); }
    public function livreur() { return $this->belongsTo(User::class, 'livreur_id'); }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'variant_id', 'size', 'color')->withTimestamps();
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->isAdmin())   return $query;
        if ($user->isVendeur()) return $query->where('seller_id', $user->id);
        if ($user->isLivreur()) return $query->where('livreur_id', $user->id);
        return $query->where('user_id', $user->id);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Position dans la frise (-1 si annulée) */
    public function getFlowIndexAttribute(): int
    {
        $i = array_search($this->status, self::FLOW, true);
        return $i === false ? -1 : $i;
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, self::ACTIVE, true);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getIncidentLabelAttribute(): ?string
    {
        return $this->incident_type ? (self::INCIDENTS[$this->incident_type] ?? $this->incident_type) : null;
    }

    public function getPaymentLabelAttribute(): string
    {
        return config('shop.payment_methods.' . ($this->payment_method ?: 'cash') . '.label', 'Espèces à la livraison');
    }

    public function getHasExactLocationAttribute(): bool
    {
        return $this->delivery_lat !== null && $this->delivery_lng !== null;
    }

    /** Lien Google Maps : position exacte si connue, sinon l'adresse saisie. */
    public function getMapUrlAttribute(): string
    {
        if ($this->has_exact_location) {
            return 'https://www.google.com/maps/search/?api=1&query=' . $this->delivery_lat . ',' . $this->delivery_lng;
        }
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode((string) $this->shipping_address);
    }

    public function getSellerNameAttribute(): string
    {
        return $this->seller ? $this->seller->display_name : 'E-Shop SN';
    }
}
