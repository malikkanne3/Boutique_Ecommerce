<?php
namespace App\Models;

use App\Models\Concerns\HasAttachment;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasAttachment;

    protected $fillable = [
        'sender_id', 'receiver_id', 'body', 'is_read',
        'type', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size',
        'lat', 'lng', 'forwarded_from',
    ];

    public function sender()   { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
}
