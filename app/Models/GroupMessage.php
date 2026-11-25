<?php
namespace App\Models;

use App\Models\Concerns\HasAttachment;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasAttachment;

    protected $fillable = [
        'group_id', 'sender_id', 'body',
        'type', 'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size',
        'lat', 'lng', 'forwarded_from',
    ];

    public function group()  { return $this->belongsTo(ChatGroup::class, 'group_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
}
