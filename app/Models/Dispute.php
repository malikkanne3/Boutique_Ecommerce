<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    public const REASONS = [
        'damaged'      => 'Produit abîmé',
        'not_conform'  => 'Produit non conforme',
        'missing'      => 'Article manquant',
        'not_received' => 'Commande non reçue',
        'other'        => 'Autre problème',
    ];

    public const STATUSES = [
        'open'           => 'Ouvert',
        'seller_replied' => 'Réponse du vendeur',
        'escalated'      => 'En arbitrage',
        'resolved'       => 'Résolu',
    ];

    public const PILLS = ['open' => 'pill-warn', 'seller_replied' => 'pill-confirmed', 'escalated' => 'pill-bad', 'resolved' => 'pill-ok'];

    public const RESOLUTIONS = [
        'refund'    => 'Remboursement',
        'exchange'  => 'Échange du produit',
        'no_action' => 'Aucune suite donnée',
        'agreement' => 'Accord trouvé entre les parties',
    ];

    protected $fillable = ['order_id', 'user_id', 'seller_id', 'reason', 'description', 'status', 'resolution', 'admin_decision', 'resolved_at'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function order()    { return $this->belongsTo(Order::class); }
    public function client()   { return $this->belongsTo(User::class, 'user_id'); }
    public function seller()   { return $this->belongsTo(User::class, 'seller_id'); }
    public function messages() { return $this->hasMany(DisputeMessage::class)->oldest(); }

    public function getReasonLabelAttribute(): string  { return self::REASONS[$this->reason] ?? 'Autre problème'; }
    public function getStatusLabelAttribute(): string  { return self::STATUSES[$this->status] ?? $this->status; }
    public function getPillClassAttribute(): string    { return self::PILLS[$this->status] ?? 'pill-off'; }
    public function getResolutionLabelAttribute(): ?string { return $this->resolution ? (self::RESOLUTIONS[$this->resolution] ?? $this->resolution) : null; }
    public function getIsOpenAttribute(): bool         { return $this->status !== 'resolved'; }
}
