<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public const AUDIENCES = [
        'all'     => 'Tout le monde',
        'client'  => 'Clients',
        'vendeur' => 'Vendeurs',
        'livreur' => 'Livreurs',
    ];

    protected $fillable = ['sender_id', 'title', 'body', 'audience'];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }

    public function getAudienceLabelAttribute(): string
    {
        return self::AUDIENCES[$this->audience] ?? 'Tout le monde';
    }
}
