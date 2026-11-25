<?php
namespace App\Models;

use App\Models\Concerns\HasAttachment;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use HasAttachment;

    protected $fillable = ['dispute_id', 'user_id', 'body', 'type', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size'];

    public function dispute() { return $this->belongsTo(Dispute::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
