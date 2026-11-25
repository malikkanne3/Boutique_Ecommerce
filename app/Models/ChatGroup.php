<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id')
            ->withPivot('last_read_at')->withTimestamps();
    }

    public function messages() { return $this->hasMany(GroupMessage::class, 'group_id'); }

    public function isCreator(User $user): bool
    {
        return (int) $this->created_by === (int) $user->id;
    }

    /** Nombre de messages non lus pour un membre (via pivot ou lecture directe). */
    public function unreadFor(User $user): int
    {
        $lastRead = $this->pivot ? $this->pivot->last_read_at : $this->members()->where('users.id', $user->id)->value('chat_group_members.last_read_at');

        $q = GroupMessage::where('group_id', $this->id)->where('sender_id', '!=', $user->id);
        if ($lastRead) {
            $q->where('created_at', '>', $lastRead);
        }
        return $q->count();
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }
}
