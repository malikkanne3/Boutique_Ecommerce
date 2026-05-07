<?php
namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\User;
use App\Helpers\NotifHelper;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $conversations = User::where('id', '!=', $userId)
            ->whereHas('sentMessages', fn($q) => $q->where('receiver_id', $userId))
            ->orWhereHas('receivedMessages', fn($q) => $q->where('sender_id', $userId))
            ->with(['sentMessages' => fn($q) => $q->where('receiver_id', $userId)->latest()->limit(1),
                    'receivedMessages' => fn($q) => $q->where('sender_id', $userId)->latest()->limit(1)])
            ->get();
        $users = User::where('id', '!=', $userId)->get();
        return view('messages.index', compact('conversations', 'users'));
    }

    public function conversation(User $user)
    {
        $messages = Message::where(function($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->oldest()->get();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->update(['is_read' => true]);

        $users = User::where('id', '!=', auth()->id())->get();
        return view('messages.conversation', compact('messages', 'user', 'users'));
    }

    public function send(User $user)
    {
        request()->validate(['body' => 'required|string|max:1000']);

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $user->id,
            'body'        => request('body'),
        ]);

        // Notifier le destinataire
        NotifHelper::send(
            $user->id,
            'Nouveau message de ' . auth()->user()->name,
            request('body'),
            'chat-dots',
            'info',
            '/messages/' . auth()->id()
        );

        return redirect()->route('messages.conversation', $user);
    }
}