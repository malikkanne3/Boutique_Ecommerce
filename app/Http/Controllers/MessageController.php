<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsChatSidebar;
use App\Models\ChatGroup;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\Notifier;
use App\Support\ChatPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    use BuildsChatSidebar;

    public function index()
    {
        // L'admin ne discute avec personne : il envoie des annonces
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.announcements.index');
        }

        return view('messages.index', $this->chatSidebar() + [
            'active' => null, 'activeGroup' => null, 'messages' => collect(),
        ]);
    }

    public function conversation(User $user)
    {
        abort_unless(auth()->user()->canMessage($user), 403);

        $me = auth()->id();
        $messages = Message::with('sender')->where(function ($q) use ($user, $me) {
            $q->where('sender_id', $me)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user, $me) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me);
        })->oldest()->get();

        Message::where('sender_id', $user->id)->where('receiver_id', $me)->update(['is_read' => true]);

        return view('messages.index', $this->chatSidebar() + [
            'active' => $user, 'activeGroup' => null, 'messages' => $messages,
        ]);
    }

    public function send(User $user)
    {
        abort_unless(auth()->user()->canMessage($user), 403);
        request()->validate(ChatPayload::rules());

        $payload = ChatPayload::build(request());
        if (! $payload) {
            return back()->with('error', 'Écrivez un message, joignez un fichier ou partagez votre position.');
        }

        $message = Message::create($payload + ['sender_id' => auth()->id(), 'receiver_id' => $user->id]);

        Notifier::notify($user->id, 'Nouveau message de ' . auth()->user()->name, $message->preview_text, 'chat-dots', 'info', '/messages/' . auth()->id(), auth()->id());

        return redirect()->route('messages.conversation', $user);
    }

    /** Transférer un message (texte, photo, fichier, position) à un contact ou à un groupe. */
    public function forward(Request $request)
    {
        $data = $request->validate([
            'kind'       => 'required|in:direct,group',
            'message_id' => 'required|integer',
            'target'     => ['required', 'regex:/^(user|group):\d+$/'],
        ]);
        $me = auth()->user();

        // Message source : on doit y avoir accès
        if ($data['kind'] === 'group') {
            $source = GroupMessage::with('sender')->findOrFail($data['message_id']);
            abort_unless($source->group && $source->group->hasMember($me), 403);
        } else {
            $source = Message::with('sender')->findOrFail($data['message_id']);
            abort_unless((int) $source->sender_id === $me->id || (int) $source->receiver_id === $me->id, 403);
        }
        $payload = ChatPayload::forwardOf($source, optional($source->sender)->display_name ?? 'Utilisateur');

        [$targetType, $targetId] = explode(':', $data['target']);

        if ($targetType === 'user') {
            $dest = User::findOrFail($targetId);
            abort_unless($me->canMessage($dest), 403, 'Vous ne pouvez pas écrire à cette personne.');
            $m = Message::create($payload + ['sender_id' => $me->id, 'receiver_id' => $dest->id]);
            Notifier::notify($dest->id, 'Nouveau message de ' . $me->name, $m->preview_text, 'chat-dots', 'info', '/messages/' . $me->id, $me->id);
            return redirect()->route('messages.conversation', $dest)->with('success', 'Message transféré à ' . $dest->display_name . '.');
        }

        $group = ChatGroup::findOrFail($targetId);
        abort_unless($group->hasMember($me), 403);
        $m = GroupMessage::create($payload + ['group_id' => $group->id, 'sender_id' => $me->id]);
        GroupController::notifyMembers($group, $me, $m);
        return redirect()->route('groups.show', $group)->with('success', 'Message transféré dans « ' . $group->name . ' ».');
    }

    /** Sert une pièce jointe privée (photo / fichier) après vérification des droits. */
    public function attachment(string $kind, int $id)
    {
        $me = auth()->user();

        if ($kind === 'dispute') {
            $m = \App\Models\DisputeMessage::with('dispute')->findOrFail($id);
            $d = $m->dispute;
            abort_unless($d && ($me->isAdmin() || (int) $d->user_id === $me->id || ($d->seller_id && (int) $d->seller_id === $me->id)), 403);
        } elseif ($kind === 'group') {
            $m = GroupMessage::findOrFail($id);
            abort_unless($m->group && $m->group->hasMember($me), 403);
        } else {
            $m = Message::findOrFail($id);
            abort_unless((int) $m->sender_id === $me->id || (int) $m->receiver_id === $me->id, 403);
        }

        abort_unless($m->attachment_path && Storage::disk('local')->exists($m->attachment_path), 404);
        $path = Storage::disk('local')->path($m->attachment_path);

        if ($m->type === 'image') {
            return response()->file($path, ['Content-Type' => $m->attachment_mime ?: 'image/jpeg', 'X-Content-Type-Options' => 'nosniff']);
        }
        return response()->download($path, $m->attachment_name ?: basename($path));
    }
}
