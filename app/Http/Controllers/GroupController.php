<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsChatSidebar;
use App\Models\ChatGroup;
use App\Models\GroupMessage;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notifier;
use App\Support\ChatPayload;

/** Groupes de discussion : on ne peut inviter que des personnes que l'on a le droit de contacter. */
class GroupController extends Controller
{
    use BuildsChatSidebar;

    private function ensureChatUser(): void
    {
        abort_if(auth()->user()->isAdmin(), 403, 'L\'administrateur utilise les annonces.');
    }

    private function ensureMember(ChatGroup $group): void
    {
        $this->ensureChatUser();
        abort_unless($group->hasMember(auth()->user()), 403);
    }

    private function ensureCreator(ChatGroup $group): void
    {
        $this->ensureMember($group);
        abort_unless($group->isCreator(auth()->user()), 403, 'Seul le créateur du groupe peut faire cela.');
    }

    /** Notifie les autres membres (une seule notification non lue par groupe). */
    public static function notifyMembers(ChatGroup $group, User $sender, GroupMessage $message): void
    {
        $link = '/groupes/' . $group->id;
        foreach ($group->members()->where('users.id', '!=', $sender->id)->pluck('users.id') as $uid) {
            $already = Notification::where('user_id', $uid)->where('link', $link)->where('is_read', false)->exists();
            if (! $already) {
                Notifier::notify($uid, '👥 ' . $group->name, $sender->name . ' : ' . $message->preview_text, 'people', 'info', $link, $sender->id);
            }
        }
    }

    public function create()
    {
        $this->ensureChatUser();
        return view('messages.group-create', ['contacts' => auth()->user()->contactableUsers()]);
    }

    public function store()
    {
        $this->ensureChatUser();
        $me = auth()->user();

        $data = request()->validate([
            'name'      => 'required|string|max:100',
            'members'   => 'required|array|min:1|max:30',
            'members.*' => 'integer',
        ]);

        $allowed = $me->contactableUsers()->pluck('id')->all();
        $ids = collect($data['members'])->map(fn ($i) => (int) $i)->filter(fn ($i) => in_array($i, $allowed, true))->unique()->values()->all();
        if (empty($ids)) {
            return back()->withInput()->withErrors(['members' => 'Choisissez au moins un membre parmi vos contacts.']);
        }

        $group = ChatGroup::create(['name' => $data['name'], 'created_by' => $me->id]);
        $group->members()->attach(array_merge([$me->id], $ids));

        foreach ($ids as $uid) {
            Notifier::notify($uid, '👥 Nouveau groupe', "{$me->name} vous a ajouté au groupe « {$group->name} ».", 'people', 'info', '/groupes/' . $group->id, $me->id);
        }

        return redirect()->route('groups.show', $group)->with('success', 'Groupe créé !');
    }

    public function show(ChatGroup $group)
    {
        $this->ensureMember($group);
        $me = auth()->user();

        $messages = $group->messages()->with('sender')->oldest()->get();
        $group->members()->updateExistingPivot($me->id, ['last_read_at' => now()]);
        $group->load('members', 'creator');

        $allowed = $me->contactableUsers()->reject(fn ($u) => $group->members->contains('id', $u->id))->values();

        return view('messages.index', $this->chatSidebar() + [
            'active' => null, 'activeGroup' => $group, 'messages' => $messages, 'addable' => $allowed,
        ]);
    }

    public function send(ChatGroup $group)
    {
        $this->ensureMember($group);
        request()->validate(ChatPayload::rules());

        $payload = ChatPayload::build(request());
        if (! $payload) {
            return back()->with('error', 'Écrivez un message, joignez un fichier ou partagez votre position.');
        }

        $message = GroupMessage::create($payload + ['group_id' => $group->id, 'sender_id' => auth()->id()]);
        self::notifyMembers($group, auth()->user(), $message);

        return redirect()->route('groups.show', $group);
    }

    public function addMembers(ChatGroup $group)
    {
        $this->ensureCreator($group);
        $me = auth()->user();
        $data = request()->validate(['members' => 'required|array|min:1|max:30', 'members.*' => 'integer']);

        $allowed = $me->contactableUsers()->pluck('id')->all();
        $existing = $group->members()->pluck('users.id')->all();
        $ids = collect($data['members'])->map(fn ($i) => (int) $i)
            ->filter(fn ($i) => in_array($i, $allowed, true) && ! in_array($i, $existing, true))->unique()->values()->all();

        if (empty($ids)) {
            return back()->with('error', 'Aucun nouveau membre valide.');
        }
        $group->members()->attach($ids);
        foreach ($ids as $uid) {
            Notifier::notify($uid, '👥 Nouveau groupe', "{$me->name} vous a ajouté au groupe « {$group->name} ».", 'people', 'info', '/groupes/' . $group->id, $me->id);
        }
        return back()->with('success', count($ids) . ' membre(s) ajouté(s).');
    }

    public function removeMember(ChatGroup $group, User $user)
    {
        $this->ensureCreator($group);
        if ($group->isCreator($user)) {
            return back()->with('error', 'Le créateur ne peut pas être retiré.');
        }
        $group->members()->detach($user->id);
        return back()->with('success', $user->name . ' a été retiré du groupe.');
    }

    public function leave(ChatGroup $group)
    {
        $this->ensureMember($group);
        if ($group->isCreator(auth()->user())) {
            return back()->with('error', 'Le créateur ne peut pas quitter son groupe : supprimez-le si vous n\'en avez plus besoin.');
        }
        $group->members()->detach(auth()->id());
        return redirect()->route('messages.index')->with('success', 'Vous avez quitté le groupe.');
    }

    public function destroy(ChatGroup $group)
    {
        $this->ensureCreator($group);
        $group->messages()->delete();
        $group->members()->detach();
        $group->delete();
        return redirect()->route('messages.index')->with('success', 'Groupe supprimé.');
    }
}
