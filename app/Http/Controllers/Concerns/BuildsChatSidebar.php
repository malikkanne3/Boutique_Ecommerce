<?php
namespace App\Http\Controllers\Concerns;

use App\Models\Message;
use App\Models\User;

trait BuildsChatSidebar
{
    /** Données communes à la page de messagerie (conversations, groupes, contacts, cibles de transfert). */
    protected function chatSidebar(): array
    {
        $self = auth()->user();
        $me   = $self->id;

        $byPartner = Message::where('sender_id', $me)->orWhere('receiver_id', $me)
            ->latest()->get()
            ->groupBy(fn ($m) => $m->sender_id == $me ? $m->receiver_id : $m->sender_id);
        $partners = User::whereIn('id', $byPartner->keys())->get()->keyBy('id');

        $conversations = $byPartner->map(function ($msgs, $partnerId) use ($partners, $me) {
            return (object) [
                'user'   => $partners->get($partnerId),
                'last'   => $msgs->first(),
                'unread' => $msgs->where('receiver_id', $me)->where('is_read', false)->count(),
            ];
        })->filter(fn ($c) => $c->user && $self->canMessage($c->user))->values();

        $groups = $self->chatGroups()->get()->map(function ($g) use ($self) {
            $g->unread_count = $g->unreadFor($self);
            $g->last_message = $g->messages()->latest()->first();
            return $g;
        })->sortByDesc(fn ($g) => optional($g->last_message)->created_at ?? $g->created_at)->values();

        $contacts = $self->contactableUsers();

        $forwardTargets = [
            'Contacts' => $contacts->map(fn ($u) => ['value' => 'user:' . $u->id, 'label' => $u->display_name . ' — ' . $u->role_label])->all(),
            'Groupes'  => $groups->map(fn ($g) => ['value' => 'group:' . $g->id, 'label' => $g->name])->all(),
        ];

        return compact('conversations', 'groups', 'contacts', 'forwardTargets');
    }
}
