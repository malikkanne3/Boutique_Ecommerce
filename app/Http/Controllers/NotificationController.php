<?php
namespace App\Http\Controllers;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return redirect($notification->link ?? back());
    }

    public function readAll()
    {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Toutes les notifications marquées comme lues.');
    }
}