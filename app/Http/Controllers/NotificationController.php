<?php
namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('actor')->where('user_id', auth()->id())->latest()->paginate(20);
        $unread = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        return view('notifications.index', compact('notifications', 'unread'));
    }

    public function markRead(Notification $notification)
    {
        abort_unless((int) $notification->user_id === (int) auth()->id(), 403);
        $notification->update(['is_read' => true]);
        return $notification->link ? redirect($notification->link) : back();
    }

    public function readAll()
    {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return back()->with('success', 'Toutes les notifications sont marquées comme lues.');
    }
}
